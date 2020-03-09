<?php

namespace Console\App\Commands;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\Output;
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

        $output->writeln(sprintf('<info>Relax and lets Create %s </info>', $project));

        // $ctx = stream_context_create();
        // stream_context_set_params($ctx, array("notification" => "stream_notification_callback"));
        // $fileData = @file_get_contents('https://codeload.github.com/Tumi-D/getInnotized/zip/master', false, $ctx);
        // $output->writeln(sprintf('<info>This is the size of our file, %s </info>', $fileData));


        file_put_contents(
            $project . ".zip",
            file_get_contents("https://codeload.github.com/Tumi-D/getInnotized/zip/master")
        );
        $this->unzip($project . ".zip", $output);
        $this->name($project, $output);
        $end_time = microtime(true);
        $execution_time = (string) ($end_time - $start_time);
        $execution_time = substr($execution_time, 0, 8);
        $output->writeln(sprintf('<info>Hope %s is something amazing. Goodluck ! %s secs </info>', $input->getArgument('projectname'), $execution_time));
        return 0;
    }

    private function unzip($file, OutputInterface $output)
    {
        $filename = substr($file, 0, -4);
        $unzip = new ZipArchive;
        $out = $unzip->open($file);
        if ($out === TRUE) {
            $unzip->extractTo(getcwd());
            $unzip->close();
            $this->delete($filename, $output);
            // getInnotized-master
            // $output->writeln(sprintf('<info>%s unzipped successfully  </info>', $file));
        } else {
            $output->writeln(sprintf('<error>Failed to unzip %s </error>', $file));
        }
    }

    private function name($name, OutputInterface $output)
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

        $path = dirname(dirname(dirname(dirname(__FILE__))));

        rename($path . '\getInnotized-master', $path . '\\' . $name);
    }

    private function delete($file, Output $output)
    {
        $path = dirname(dirname(dirname(dirname(__FILE__))));

        $file_pointer =  $path . '\\' . $file . ".zip";

        // Use unlink() function to delete a file  
        if (!unlink($file_pointer)) {
            $output->writeln(sprintf('<error> %s cannot be deleted due to an error </error>', $file));
        }
    }
}
