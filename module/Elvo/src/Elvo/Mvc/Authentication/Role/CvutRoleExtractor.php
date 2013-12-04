<?php

namespace Elvo\Mvc\Authentication\Role;

use Elvo\Mvc\Authentication\Identity;
use Elvo\Util\Options;


class CvutRoleExtractor implements RoleExtractorInterface
{

    const OPT_VALUE_DELIMITER = 'value_delimiter';

    const OPT_FIELD_DELIMITER = 'field_delimiter';

    const OPT_DEPARTMENT_CODE = 'department_code';

    const OPT_ROLE_MAP = 'role_map';

    /**
     * @var Options
     */
    protected $options;

    /**
     * @var array
     */
    protected $defaultOptions = array(
        self::OPT_VALUE_DELIMITER => ';',
        self::OPT_FIELD_DELIMITER => ':',
        self::OPT_DEPARTMENT_CODE => '13',
        self::OPT_ROLE_MAP => array(
            'ZAMESTNANEC-AKADEMICKY' => Identity::ROLE_ACADEMIC,
            'STUDENT' => Identity::ROLE_STUDENT
        )
    );


    /**
     * Constructor.
     * 
     * @param Options $options
     */
    public 

    function __construct(Options $options = null)
    {
        if (null === $options) {
            $options = new Options();
        }
        
        $this->setOptions($options);
    }


    /**
     * @return Options
     */
    public function getOptions()
    {
        return $this->options;
    }


    /**
     * @param Options $options
     */
    public function setOptions(Options $options)
    {
        $options->addDefaultValues($this->defaultOptions);
        $this->options = $options;
    }


    public function extractRoles($roleData)
    {
        $receivedRoles = $this->parseRoleData($roleData);
    }


    public function parseRoleData($roleData)
    {
        $roles = array();
        foreach ($this->roleDataToArray($roleData) as $roleComplexValue) {
            $roles[] = $this->extractRoleCodeFromComplexValue($roleComplexValue);
        }
        
        return $roles;
    }


    public function filterRolesByDepartmentCode(array $roles)
    {}


    /**
     * Parses the serialized string and returns an array of raw values.
     * 
     * @param string $roleData
     * @return array
     */
    public function roleDataToArray($roleData)
    {
        if (! is_string($roleData)) {
            throw new Exception\InvalidRoleDataException(sprintf("Invalid role data of type '%s'", gettype($roleData)));
        }
        
        $values = explode($this->getValueDelimiter(), $roleData);
        if (false === $values) {
            $values = array();
        }
        
        return $values;
    }


    public function extractRoleCodeFromComplexValue($roleComplexValue)
    {
        if (! is_string($roleComplexValue)) {
            throw new Exception\InvalidRoleDataException(sprintf("Invalid role complex data of type '%s'", gettype($roleComplexValue)));
        }
        
        $fields = explode($this->getFieldDelimiter(), $roleComplexValue);
        if (false === $fields) {
            return null;
        }
        
        if (empty($fields) || ! $fields[0]) {
            return null;
        }
        
        return $fields[0];
    }


    protected function getValueDelimiter()
    {
        return $this->options->get(self::OPT_VALUE_DELIMITER);
    }


    protected function getFieldDelimiter()
    {
        return $this->options->get(self::OPT_FIELD_DELIMITER);
    }


    protected function getDepartmentCode()
    {
        return $this->options->get(self::OPT_DEPARTMENT_CODE);
    }
}