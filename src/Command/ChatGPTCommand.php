<?php

namespace Ramity\Bundle\ConjureBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;

#[AsCommand( name: 'conjure:chatgpt', description: 'Allows for the injection of code provided by chatGPT' )]
class ChatGPTCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Extract text from a shared ChatGPT conversation')
            ->addArgument('conversationURL', InputArgument::REQUIRED, 'The shared chatGPT conversation URL.')
            ->addArgument('conversationRegex', InputArgument::REQUIRED, 'The regex pattern to apply on the conversation.')
            ->addArgument('filePath', InputArgument::REQUIRED, 'The file destination for the regex extracted text.')
            ->addArgument('fileLine', InputArgument::OPTIONAL, 'The line number of where the regex extracted text will start.')
            ->addArgument('mode', InputArgument::OPTIONAL, 'The mode for modification (insert, replace).')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $conversationURL = $input->getArgument('conversationURL');

        // Setup and run curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $conversationURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0');
        $response = curl_exec($ch);
        curl_close($ch);

        // Validate
        if ($response === false)
        {
            $output->writeln('Error: ' . curl_error($ch));
            return Command::FAILURE;
        }

        // Crawl DOM response, extract and parse json data, extract assistant response.
        $crawler = new Crawler($response);
        $element = $crawler->filter('script#__NEXT_DATA__');
        $responseDictionary = json_decode($element->text());
        $mappings = $responseDictionary->props->pageProps->serverResponse->data->mapping;
        $firstMappingKey = reset($mappings)->id;
        $firstMappingDictionary = $mappings->$firstMappingKey;
        $assistantResponseText = $firstMappingDictionary->message->content->parts[0];

        echo $assistantResponseText;

        // Apply regex to the assistant response text
        //$pattern = $input->getArgument('conversationRegex');
        //$result = preg_match($pattern, $assistantResponseText, $matches);

        // Validate
        // if ($result == 0)
        // {
        //     $output->writeln('Error: ' . $pattern . ' get not obtain any matches.');
        //     return Command::FAILURE;
        // }

        // $assistantResponseTextSelection = $matches[1];

        // echo $assistantResponseTextSelection;

        return Command::SUCCESS;
    }
}
