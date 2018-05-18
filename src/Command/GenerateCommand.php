<?php
namespace App\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('generatecodes')
            ->addArgument(
                'numberOfCodes',
                InputArgument::REQUIRED
            )->addArgument(
                'codeLength',
                InputArgument::REQUIRED
            )->addArgument(
                'fileName',
                InputArgument::REQUIRED
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->generate($input, $output);
    }

    private function generate(InputInterface $input, OutputInterface $output)
    {
        $numberOfCodes = $input->getArgument('numberOfCodes');
        $codeLength = $input->getArgument('codeLength');
        $fileName = $input->getArgument('fileName');

        $generatingService = $this->getContainer()->get('generator');

        if (!$generatingService->checkValue($numberOfCodes)) {
            $output->write('Wrong value for number of codes');
        }
        if (!$generatingService->checkValue($codeLength)) {
            $output->write('Wrong value for code length');
        }
        if (!$generatingService->checkFileString($fileName)) {
            $output->write('Wrong path for file to save');
        }

        if ($generatingService->errors) {
            return;
        }
        $generatingService->generate($numberOfCodes, $codeLength, $fileName);

        $output->write("Codes saved to {$fileName}");
    }
}