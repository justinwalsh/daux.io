<?php namespace Todaymade\Daux\Console;

use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\ProcessUtils;
use Todaymade\Daux\Daux;
use Todaymade\Daux\Server\Server;

class Serve extends DauxCommand
{
    protected function configure()
    {
        $this
            ->setName('serve')
            ->setDescription('Serve documentation')

            ->addOption('configuration', 'c', InputOption::VALUE_REQUIRED, 'Configuration file')
            ->addOption('source', 's', InputOption::VALUE_REQUIRED, 'Where to take the documentation from')
            ->addOption('processor', 'p', InputOption::VALUE_REQUIRED, 'Manipulations on the tree')
            ->addOption('themes', 't', InputOption::VALUE_REQUIRED, 'Set a different themes directory')

            // Serve the current documentation
            ->addOption('serve', null, InputOption::VALUE_NONE, 'Serve the current directory')
            ->addOption('host', null, InputOption::VALUE_REQUIRED, 'The host to serve on', 'localhost')
            ->addOption('port', null, InputOption::VALUE_REQUIRED, 'The port to serve on', 8085);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $host = $input->getOption('host');
        $port = $input->getOption('port');

        $daux = $this->prepareDaux($input);

        // Daux can only serve HTML
        $daux->getParams()->setFormat('html');

        chdir(__DIR__ . '/../../');

        putenv('DAUX_SOURCE=' . $daux->getParams()->getDocumentationDirectory());
        putenv('DAUX_THEME=' . $daux->getParams()->getThemesPath());
        putenv('DAUX_CONFIGURATION=' . $daux->getParams()->getConfigurationOverrideFile());
		putenv('DAUX_EXTENSION=' . DAUX_EXTENSION);

        $base = ProcessUtils::escapeArgument(__DIR__ . '/../../');
        $binary = ProcessUtils::escapeArgument((new PhpExecutableFinder)->find(false));

        echo "Daux development server started on http://{$host}:{$port}/\n";

        if (defined('HHVM_VERSION')) {
            if (version_compare(HHVM_VERSION, '3.8.0') >= 0) {
                passthru("{$binary} -m server -v Server.Type=proxygen -v Server.SourceRoot={$base}/ -v Server.IP={$host} -v Server.Port={$port} -v Server.DefaultDocument=server.php -v Server.ErrorDocument404=server.php");
            } else {
                throw new Exception("HHVM's built-in server requires HHVM >= 3.8.0.");
            }
        } else {
            passthru("{$binary} -S {$host}:{$port} {$base}/index.php");
        }
    }
}
