<?php

namespace Lilweb\ExtraBundle\Features\Context;

use Behat\Behat\Exception\BehaviorException;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Symfony2Extension\Context\KernelAwareInterface;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Contexte pour le testing des emails.
 */
class EmailContext extends RawMinkContext implements KernelAwareInterface
{
    /**
     * @var KernelInterface Le noyau de l'application.
     */
    private $kernel;

    /**
     * @inheritdoc
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @Given /^(?:|the )"(?P<type>[^"]+)" mail should be sent to "(?P<email>[^"]+)"$/
     */
    public function theMailShouldBeSentTo($type, $email)
    {
        // Obligé de rajouter un sleep pour etre sur que l'email ait pu être ecrit sur le disque.
        sleep(1);

        $spoolDir = $this->getSpoolDir();
        $filesystem = new Filesystem();

        if ($filesystem->exists($spoolDir)) {
            $finder = new Finder();

            // find every files inside the spool dir except hidden files
            $finder
                ->in($spoolDir)
                ->ignoreDotFiles(true)
                ->files();

            if ($finder->count() == 0) {
                throw new BehaviorException(sprintf("No emails were sent"));
            }

            foreach ($finder as $file) {
                $message = unserialize(file_get_contents($file));

                // check the recipients
                $recipients = array_keys($message->getTo());
                if (!in_array($email, $recipients)) {
                    continue;
                }

                // check if this is the correct message type
                $headers = $message->getHeaders();
                if ($headers->has('X-Message-ID')) {
                    $messageId = $headers->get('X-Message-ID')->getValue();

                    if ($messageId == $type) {
                        return;
                    }
                }
            }
        } else {
            throw new BehaviorException("The spool folder could not be opened");
        }

        throw new BehaviorException(sprintf("The \"%s\" was not sent", $type));
    }

    /**
     * Cleaning the Swiftmailer file before each scenario.
     *
     * @BeforeScenario
     */
    public function purgeSpool()
    {
        $spoolDir = $this->getSpoolDir();

        $filesystem = new Filesystem();
        $filesystem->remove($spoolDir);
    }

    /**
     * @return mixed
     */
    private function getSpoolDir()
    {
        return $this->kernel->getContainer()->getParameter('swiftmailer.spool.default.file.path');
    }
}
