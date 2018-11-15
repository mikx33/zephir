<?php

/**
 * This file is part of the Zephir.
 *
 * (c) Zephir Team <team@zephir-lang.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zephir\Command;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Zephir\Command\BuildCommand
 *
 * Generates/Compiles/Installs a Zephir extension.
 *
 * @package Zephir\Command
 */
class BuildCommand extends ContainerAwareCommand implements DevelopmentModeAwareInterface, ZflagsAwareInterface
{
    use DevelopmentModeAwareTrait;
    use ZflagsAwareTrait;

    protected function configure()
    {
        $this
            ->setName('build')
            ->setDescription('Generates/Compiles/Installs a Zephir extension')
            ->addOption(
                'backend',
                null,
                InputOption::VALUE_REQUIRED,
                'Used backend to build extension',
                'ZendEngine3'
            )
            ->addOption('dev', null, InputOption::VALUE_NONE, 'Build the extension in development mode')
            ->addOption('no-dev', null, InputOption::VALUE_NONE, 'Build the extension in production mode')
            ->setHelp($this->getDevelopmentModeHelp(). PHP_EOL . PHP_EOL . $this->getZflagsHelp());
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = $this->getApplication()->find('install');

        $arguments = [
            'command'   => 'install',
            '--backend' => $input->getOption('backend'),
            '--dev'     => $this->isDevelopmentModeEnabled($input),
        ];

        return $command->run(new ArrayInput($arguments), $output);
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getDevelopmentModeHelp()
    {
        return <<<EOT
Generates/Compiles/Installs a Zephir extension.
Just a meta command that just calls "generate", "compile" and "install" commands.

Using "--dev" option will force building and installing the extension in development mode
(debug symbols and no optimizations). An extension compiled with debugging symbols means
you can run a program or library through a debugger and the debugger's output will be user
friendlier. These debugging symbols also enlarge the program or library significantly.

Note: Zephir development mode will be enabled silently if your PHP binary was compiled in
a debug configuration.

In some cases, we would like to get production ready extension even if the PHP binary was
compiled in a debug configuration. Use "--no-dev" option to achieve this behavior.
EOT;
    }
}
