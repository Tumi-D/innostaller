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

use ZipArchive;

class CreateProjectCommand extends Command
{
    protected function configure()
    {
        $this->setName('create')
            ->setDescription('Scaffolds a new project')
            ->setHelp('Install globally and add a name and you can  scaffold a fresh project')
            ->addArgument('projectname', InputArgument::REQUIRED, 'Pass the username.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $start_time = microtime(true);
        $project = $input->getArgument('projectname');
        $project = ucfirst($project);
        $directory = $project && $project !== '.' ? getcwd() . '/' . $project : getcwd();

        $output->writeln(sprintf('<info>Relax and lets Create %s </info>', $project));
        // $composer = $this->findComposer();
        $commands = [
            // $composer . ' install --no-scripts',
            // $composer . ' run-script post-root-package-install',
            // $composer . ' run-script post-create-project-cmd',
            // $composer . ' run-script post-autoload-dump',
        ];
        // $ctx = stream_context_create();
        // stream_context_set_params($ctx, array("notification" => "stream_notification_callback"));
        // $fileData = @file_get_contents('https://codeload.github.com/Tumi-D/getInnotized/zip/master', false, $ctx);
        // $output->writeln(sprintf('<info>This is the size of our file, %s </info>', $fileData));


        file_put_contents(
            $project . ".zip",
            file_get_contents("https://codeload.github.com/Tumi-D/getInnotized/zip/master")
        );
        $this->unzip($project . ".zip", $output, $directory);
        $this->name($project, $output, $directory);
        $end_time = microtime(true);
        $execution_time = (string) ($end_time - $start_time);
        $execution_time = substr($execution_time, 0, 8);
        // $process = Process::fromShellCommandline(implode(' && ', $commands), $directory, null, null, null);

        // if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
        //     try {
        //         $process->setTty(true);
        //     } catch (RuntimeException $e) {
        //         $output->writeln('Warning: ' . $e->getMessage());
        //     }
        // }

        // $process->run(function ($type, $line) use ($output) {
        //     $output->write($line);
        // });
        // if ($process->isSuccessful()) {
        //     $output->writeln('<comment>Hope %s is something amazing. Goodluck ! %s secs</comment>', $input->getArgument('projectname'), $execution_time);
        // }
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
            // getInnotized-master
            // $output->writeln(sprintf('<info>%s unzipped successfully  </info>', $file));
        } else {
            $output->writeln(sprintf('<error>Failed to unzip %s </error>', $file));
        }
    }

    private function name($name, OutputInterface $output, $path)
    {
        // Create arrays with special chars
        // $o = array('Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'ò', 'ó', 'ô', 'õ', 'ö');
        // // Remember to remove the slash at the end otherwise it will not work
        // $oldname = 'getInnotized-master';
        // // Get the directory name
        // $old_dir_name = substr($oldname, strrpos($oldname, '/') + 1);

        // // Replace any special chars with your choice
        // $new_dir_name = str_replace($o, 'O', $old_dir_name);

        // // Define the new directory
        // $newname = '/path/to/new_directory/' . $new_dir_name;

        // // Renames the directory
        // rename($oldname, $newname);
        // realpath(dirname(__FILE__))

        // $path = dirname(dirname(dirname(dirname(__FILE__))));

        rename($path . '\getInnotized-master', $path . '\\' . $name);
    }

    private function delete($file, Output $output, $path)
    {
        // $path = dirname(dirname(dirname(dirname(__FILE__))));

        $file_pointer =  $path . ".zip";
        @chmod($file_pointer, 0777);

        // Use unlink() function to delete a file  
        if (!unlink($file_pointer)) {
            $output->writeln(sprintf('<error> %s cannot be deleted due to an error </error>', $file));
        }
    }

    /**
     * Get the composer command for the environment.
     *
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
}
