<?php

namespace Elvo\Console\Command;

use Symfony\Component\Console\Input\InputOption;
use Elvo\Util\Exception\MissingOptionException;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Command\Command;
use Zend\Db;


class DbInit extends Command
{

    const OPT_INIT_SCRIPT = 'init_script';

    const OPT_CLEAR_SCRIPT = 'clear_script';

    /**
     * @var Db\Adapter\Adapter
     */
    protected $dbAdapter;

    /**
     * @var array
     */
    protected $options;


    /**
     * Constructor.
     * 
     * @param Db\Adapter\Adapter $dbAdapter
     * @param unknown_type $initScript
     */
    public function __construct(Db\Adapter\Adapter $dbAdapter, array $options)
    {
        $this->setDbAdapter($dbAdapter);
        $this->setOptions($options);
        
        parent::__construct();
    }


    /**
     * @return Db\Adapter\Adapter
     */
    public function getDbAdapter()
    {
        return $this->dbAdapter;
    }


    /**
     * @param Db\Adapter\Adapter $dbAdapter
     */
    public function setDbAdapter(Db\Adapter\Adapter $dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
    }


    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }


    /**
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }


    protected function configure()
    {
        $this->setName('db:init')
            ->setDescription('Initialize the database')
            ->addOption('clear', null, InputOption::VALUE_NONE, 'Delete any data from the tables');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $initScript = $this->getOption(self::OPT_INIT_SCRIPT);
        if (! $initScript) {
            throw new MissingOptionException(sprintf("Missing option '%s'", self::OPT_INIT_SCRIPT));
        }
        
        $this->executeSqlFile($initScript);
        
        if ($input->getOption('clear')) {
            $clearScript = $this->getOption(self::OPT_CLEAR_SCRIPT);
            if (! $clearScript) {
                throw new MissingOptionException(sprintf("Missing option '%s'", self::OPT_CLEAR_SCRIPT));
            }
            
            $this->executeSqlFile($clearScript);
        }
    }


    protected function executeSqlFile($sqlFile)
    {
        $dbAdapter = $this->getDbAdapter();
        
        $sql = file_get_contents($sqlFile);
        $sqlQueries = explode(';', $sql);
        
        foreach ($sqlQueries as $query) {
            $query = trim($query);
            if ($query == '') {
                continue;
            }
            $dbAdapter->query($query, Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        }
    }


    protected function getOption($name)
    {
        if (isset($this->options[$name])) {
            return $this->options[$name];
        }
        
        return null;
    }
}