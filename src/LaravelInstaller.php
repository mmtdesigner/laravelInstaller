<?php
namespace MMT;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use GuzzleHttp\Client;

class LaravelInstaller extends Command
{

    /**
     * guzzle http client
     *
     * @var object
     */
    private $client;

    /**
     * Hold the auto encrypt generated file name for laravel zip file
     *
     * @var string
     */
    private $file_name;

    public function __construct() {

        $this->client = new Client([
            'base_uri' => 'http://cabinet.laravel.com/latest.zip',
            'timeout'  => 2.0,
        ]);

        $this->file_name = $this->file_name();

        parent::__construct();

    }

    /**
     * create laravel console command with "new" as a parameter
     *
     * @return void
     */
    public function configure() {

        $this->setName("laravel")
            ->setDescription("laravel installer command line tools")
            ->addArgument('new', InputArgument::REQUIRED, 'Will grab a brandnew, raw laravel latest source');

    }

    /**
     * Handles the whole laravel package installation including:
     * 1- check to see if a package already exsits
     * 2- download a package
     * 3- unzip the package
     * 4- remove downloaded zip file
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output) {

        $output->writeln("");
        $output->writeln("<comment>Hello There, Thanks for downloading laravel.</comment>");
        $output->writeln("<comment>Laravel installation begins ...</comment>\n");

        // check to see if a laravel installation already exists or not!
        if ($this->laravel_dir_exists() && $this->laravel_dir_has_files()) {
            $output->writeln("<error>An Installation of laravel already exists! please remove \"laravel\" folder and try again ...</error>");
            return;
        }

        // create a new laravel directory
        if (!$this->laravel_dir_exists()) {
            $this->create_laravel_dir();
        }


        $output->writeln("<comment>Downloading laravel package ...</comment>");
        $this->download();
        
        
        $output->writeln("<comment>Unziping laravel package ...</comment>");
        $this->unzip();

        $output->writeln("<comment>Clearning up ...</comment>");
        $this->remove_downloded_file();

        $output->writeln("<info>Laravel was successfuly installed enjoy!</info>");

    }

    /**
     * Downloads a new laravel latest.zip file into /laravel folder
     *
     * @return void
     */
    private function download() {

        $respond = $this->client->request('GET');

        file_put_contents($this->laravel_zip_file(), $respond->getBody());

    }

    /**
     * Zip file with encrypted laravel file name.
     *
     * @return string
     */
    private function laravel_zip_file() {
        return $this->laravel_dir() . $this->file_name;
    }

    /**
     * Uses php zipArchive to unzip a laravel .zip file into /laravel folder
     *
     * @return void
     */
    private function unzip() {

        $zip = new \ZipArchive;

        $res = $zip->open($this->laravel_zip_file());
        
        if ($res === TRUE) {

            $zip->extractTo($this->laravel_dir());
            $zip->close();

        }
    }

    /**
     * removes encrypted zip file after unzip
     *
     * @return bool
     */
    private function remove_downloded_file() {

        return unlink($this->laravel_zip_file());

    }

    /**
     * Creates a new laravel document
     *
     * @return bool
     */
    private function create_laravel_dir() {

        return mkdir($this->laravel_dir());
    }

    /**
     * checks to see if /laravel folder is empty or has any files in it
     *
     * @return boolean
     */
    private function laravel_dir_has_files() {

        if (!$this->laravel_dir_exists()) return false;

        $dir = opendir($this->laravel_dir());
    
        while(false !== ($file = readdir($dir))) {

            if ($file !== '.' && $file !== '..') {
                closedir($dir);
                return true;
            }
        }

        closedir($dir);
        return false;

    }

    /**
     * checks to see if laravel directory exists
     *
     * @return boolean
     */
    private function laravel_dir_exists() {

        return file_exists($this->laravel_dir());

    }

    /**
     * Generates a new and unique encrypted file name with .zip extension
     *
     * @return string zip filename
     */
    private function file_name() {

        return "laravel-" . md5(time() . uniqid()) . ".zip";

    }

    /**
     * returns the absolute path to laravel directory
     *
     * @return string directory name
     */
    private function laravel_dir() {
        return getcwd() . '/laravel/';
    }

}