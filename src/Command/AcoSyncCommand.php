<?php
declare(strict_types=1);

/**
 * Acl Extras Command.
 *
 * Enhances the existing Acl Command with a few handy functions
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2008-2013, Mark Story.
 * @copyright Copyright 2025, Daniel Ziegler
 * @link https://mark-story.com
 * @author Mark Story <mark@mark-story.com>
 * @author Daniel Ziegler <d.ziegler@allgeier-its.com>
 * @license https://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

namespace Acl\Command;

use Acl\AclExtras;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\ORM\TableRegistry;

/**
 * AcoSync command.
 */
class AcoSyncCommand extends Command {

    /**
     * AclExtras instance
     *
     * @var \Acl\AclExtras
     */
    public AclExtras $AclExtras;

    /**
     * The name of this command.
     *
     * @var string
     */
    protected string $name = 'aco_sync';

    /**
     * Get the default command name.
     *
     * @return string
     */
    public static function defaultName(): string {
        return 'aco_sync';
    }

    /**
     * Get the command description.
     *
     * @return string
     */
    public static function getDescription(): string {
        return 'Perform a full sync on the ACO table.' .
            'Will create new ACOs or missing controllers and actions.' .
            'Will also remove orphaned entries that no longer have a matching controller/action';
    }

    /**
     * Hook method for defining this command's option parser.
     *
     * @see https://book.cakephp.org/5/en/console-commands/commands.html#defining-arguments-and-options
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
     * @return \Cake\Console\ConsoleOptionParser The built parser.
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser {
        return parent::buildOptionParser($parser)
            ->setDescription(static::getDescription())
            ->addOption('plugin', [
                'short' => 'p',
                'help'  => __('Plugin to process')
            ]);
    }

    /**
     * Implement this method with your command's logic.
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return int|null|void The exit code or null for success
     */
    public function execute(Arguments $args, ConsoleIo $io) {
        $this->AclExtras = new AclExtras();
        $this->AclExtras->startup();
        $this->AclExtras->setIo($io);

        try {
            TableRegistry::getTableLocator()->get('Aros')->getSchema();
        } catch (\Exception $e) {
            $io->out(__d('cake_acl', 'Acl database tables not found. To create them, run:'));
            $io->out('');
            $io->out('  bin/cake Migrations.migrations migrate -p Acl');
            $io->out('');

            return 1;
        }

        $this->AclExtras->acoSync([
            'plugin' => $args->getOption('plugin'),
        ]);

    }


}
