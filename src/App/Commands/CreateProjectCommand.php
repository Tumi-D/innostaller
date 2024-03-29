<?php

namespace Console\App\Commands;

use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

use ZipArchive;

class CreateProjectCommand extends Command
{
    private $options=array(
        "ssl"=>array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
        ),
    );  
    // $r = file_get_contents("https://yoursite.com/", false, stream_context_create($options));
    private $repo ="https://codeload.github.com/Tumi-D/getInnotized/zip/master";
    protected function configure()
    {
        $this->setName('create')
            ->setDescription('Scaffolds a new project')
            ->setHelp('Install globally and add a name and you can  scaffold a fresh project')
            ->addArgument('projectname', InputArgument::REQUIRED, 'Pass the username.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $outputStyle = new OutputFormatterStyle('blue', 'black', ['bold', 'blink']);
       $output->getFormatter()->setStyle('innotize', $outputStyle);
        // $project = $input->getArgument('projectname');
        $output->writeln(sprintf('<innotize> ___         _____                                                  </innotize>'));
        $output->writeln(sprintf('<innotize>|  __  _ ___   |   .  . .  .  __  ___ _ _  ___   ___  .             </innotize>'));
        $output->writeln(sprintf('<innotize>|   | |_  |    |   |\ | |\ | |  |  |   |     /   |__  |\            </innotize>'));
        $output->writeln(sprintf('<innotize>|___| |_  |  __|__ | \| | \| |__|  |  _|_   /__  |__  |_|           </innotize>'));
        $output->writeln(sprintf('<innotize>                                                                    </innotize>'));
        // $output->writeln(sprintf('<innotize>        </innotize>'));

        // $output->writeln('Get in touch with the author <href=https://symfony.com>Prince Oduro</>');
        
        $start_time = microtime(true);
        $project = $input->getArgument('projectname');
        $project = ucfirst($project);
        $directory = $project && $project !== '.' ? getcwd() . '/'  : getcwd();
        $checkdirectory = getcwd() . '/' . $project;
        if ($this->folder_exist($project) == $checkdirectory) {
            $output->writeln(sprintf('<error>Oops %s already exists </error>', $project));
            return 0;
        }

        $output->writeln(sprintf('<info>Relax and lets Create %s </info>', $project));


        // $composer = $this->findComposer();
        // $commands = [
        //     $composer . ' install --no-scripts',
        //     $composer . ' run-script post-root-package-install',
        //     $composer . ' run-script post-create-project-cmd',
        //     $composer . ' run-script post-autoload-dump',
        // ];
        // $ctx = stream_context_create();
        // stream_context_set_params($ctx, array("notification" => "stream_notification_callback"));
        // $fileData = @file_get_contents('https://codeload.github.com/Tumi-D/getInnotized/zip/master', false, $ctx);
        // $output->writeln(sprintf('<info>This is the size of our file, %s </info>', $fileData));


        file_put_contents(
            $project . ".zip",
            $file =  @file_get_contents($this->repo,false, stream_context_create($this->options))
        );
        if ($file === FALSE) {
            $error = error_get_last();

            $output->writeln(sprintf("<error>Download failed because {$error['message']} </error>"));
            exit;
        }
        // $result = $this->download($this->repo,$project . ".zip");

        $this->unzip($project . ".zip", $output, $directory);
        $this->name($project, $output, $directory);
        $end_time = microtime(true);
        $execution_time = (string) ($end_time - $start_time);
        $execution_time = substr($execution_time, 0, 4);
        $output->writeln(sprintf('<info>Hope %s is something amazing. Goodluck ! %s secs </info>', $input->getArgument('projectname'), $execution_time));     
        return 0;
    }

    private function unzip($file, OutputInterface $output, $directory)
    {
        $filename = substr($file, 0, -4);
        $unzip = new ZipArchive;
        $out = $unzip->open($file);
        if ($out === TRUE) {
            $unzip->extractTo($directory);
            $unzip->close();
            $this->delete($filename, $output, $directory);
            //getInnotized-master
            // $output->writeln(sprintf('<info>%s unzipped successfully  </info>', $file));
        } else {
            $output->writeln(sprintf('<error>Failed to unzip %s </error>', $file));
        }
    }

    private function name($name, OutputInterface $output, $path)
    {
        rename($path . '/getInnotized-master', $path . '/' . $name);
    }

    private function delete($file, Output $output, $path)
    {
        $file_pointer =  $path . '\\' . $file . ".zip";
        @chmod($file_pointer, 0777);

        // Use unlink() function to delete a file  
        if (!unlink($file_pointer)) {
            $output->writeln(sprintf('<error> %s cannot be deleted due to an error </error>', $file));
        }
    }

    /**
     * Get the composer command for the environment.
     * @return string
     */
    protected function findComposer()
    {
        $composerPath = getcwd() . '/composer.phar';

        if (file_exists($composerPath)) {
            return '"' . PHP_BINARY . '" ' . $composerPath;
        }

        return 'composer';
    }

    /** Check if folder exists
     * @return bool
     */
    protected function folder_exist($folder)
    {
        // Get canonicalized absolute pathname
        $path = realpath($folder);

        // If it exist, check if it's a directory
        return ($path !== false and is_dir($path)) ? $path : false;
    }
    protected function download($file_source, $file_target) {
        $rh = fopen($file_source, 'rb');
        $wh = fopen($file_target, 'w+b');
        if (!$rh || !$wh) {
            return false;
        }
    
        while (!feof($rh)) {
            if (fwrite($wh, fread($rh, 4096)) === FALSE) {
                return false;
            }
            echo ' ';
            flush();
        }
    
        fclose($rh);
        fclose($wh);
    
        return true;
    }
}
