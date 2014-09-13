<?php

namespace UAM\Bundle\PostmarkSwiftMailerBundle\Command;

use Swift_Message;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestEmailCommand extends ContainerAwareCommand
{
  protected function configure()
    {
        $this
            ->setName('uam:postmark:test')
            ->addArgument('recipient', InputArgument::REQUIRED, 'Email address of message recipient')
            ->setDescription("Test sending an email")
            ->setHelp(<<<EOT
The <info>uam:postmark:test</info> command tests sending an email via the PostmarkTransport class.
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $recipient = $input->getArgument('recipient');

        $from_email = $this->getContainer()->getParameter('mz_postmark.from_email');
        $from_name = $this->getContainer()->getParameter('mz_postmark.from_name');

        $message = Swift_Message::newInstance()
            ->setSubject('Test email')
            ->setFrom(array($from_email => $from_name))
            ->setTo($recipient)
            ->setBody('test message');

        $mailer = $this->getContainer()->get('mailer');

        $output->writeln(sprintf(
            'Sent %d test email to %s using %s',
            $mailer->send($message),
            $recipient,
            get_class($mailer->getTransport())
        ));
    }
}
