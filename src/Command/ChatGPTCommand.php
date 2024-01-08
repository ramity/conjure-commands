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
            ->addArgument('filePath', InputArgument::OPTIONAL, 'The file destination for the regex extracted text.')
            ->addArgument('fileLine', InputArgument::OPTIONAL, 'The line number of where the regex extracted text will start.')
            ->addArgument('mode', InputArgument::OPTIONAL, 'The mode for modification (insert, replace).')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try
        {
            $this->validate($input);
            $response = $this->request($conversationURL);
            $code = $this->process($response);
        }
        catch (exception)
        {
            $output->writeln('Error:');
            $output->writeln(exception->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    // Validate inputs
    private function validate($input)
    {
        if (!filter_var($conversationURL))
        {
            throw new Exception("The provided conversationURL is not a valid URL");
        }
    }

    private function request($url)
    {
        // Setup and softly simulate a browser request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0');

        // Perform request
        $response = curl_exec($ch);

        // Close session and free resources
        curl_close($ch);

        // Validate response
        if ($response === false)
        {
            throw new Exception(curl_error($ch));
        }

        // Return result
        return $repsonse;
    }

    private function process($response): string
    {
        // Crawl response, extract and parse json data, extract assistant response.
        $crawler = new Crawler($response);

        // Extract json data from script
        $element = $crawler->filter('script#__NEXT_DATA__');

        // Parse extracted json data
        $responseDictionary = json_decode($element->text());

        // Extract first AI response
        $mappings = $responseDictionary->props->pageProps->serverResponse->data->mapping;
        $firstMappingKey = reset($mappings)->id;
        $firstMappingDictionary = $mappings->$firstMappingKey;
        $assistantResponseText = $firstMappingDictionary->message->content->parts[0];

        // Extract code from AI response
        $pattern = '/```.*?\n(.*?)```/s';
        $result = preg_match($pattern, $assistantResponseText, $matches);

        // Validate
        if ($result == 0)
        {
            throw new Exception("No code found in the provided response.");
        }

        // Return result
        return $matches[1];
    }
}
