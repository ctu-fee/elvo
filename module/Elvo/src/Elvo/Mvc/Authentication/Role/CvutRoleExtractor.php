<?php

namespace Elvo\Mvc\Authentication\Role;

use Elvo\Mvc\Authentication\Identity;
use Elvo\Util\Options;
use Elvo\Util\Exception\MissingOptionException;


/**
 * Extracts roles from data with format according to the business roles defined in the CTU (CVUT)
 * central user database.
 */
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
        self::OPT_DEPARTMENT_CODE => null,
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


    /**
     * {@inheritdoc}
     * @see \Elvo\Mvc\Authentication\Role\RoleExtractorInterface::extractRoles()
     */
    public function extractRoles($roleData)
    {
        $receivedRoles = $this->parseRoleData($roleData);
        $filteredRoles = $this->filterRolesByDepartmentCode($receivedRoles);
        
        $roleMap = $this->getRoleMap();
        $roles = array();
        foreach ($filteredRoles as $roleInfo) {
            $roleName = $roleInfo['role_name'];
            if (isset($roleMap[$roleName])) {
                $roles[$roleName] = $roleMap[$roleName];
            }
        }
        
        return array_values($roles);
    }


    /**
     * Parses the input raw role data string into array of roles, where each item is an array
     * with keys 'department_code' and 'role_name'.
     * 
     * @param string $roleData
     * @return array
     */
    public function parseRoleData($roleData)
    {
        $roles = array();
        foreach ($this->roleDataToArray($roleData) as $roleComplexValue) {
            $roles[] = $this->parseRoleComplexValue($roleComplexValue);
        }
        
        return $roles;
    }


    /**
     * Parses a single raw role value into array with keys 'department_code' and 'role_name'.
     * 
     * For example, input like:
     * B-11000-FOO-BAR:BLA-BLA-BLA...
     * 
     * is returned as:
     * array('department_code' => '11000', 'role_name' => 'FOO-BAR')
     * 
     * @param string $roleComplexValue
     * @return array
     */
    public function parseRoleComplexValue($roleComplexValue)
    {
        return $this->parseRoleCode($this->extractRoleCodeFromComplexValue($roleComplexValue));
    }


    /**
     * Parses a single field, from the raw value, such as 'B-11000-FOO-BAR'.
     * 
     * @param string $roleCode
     * @throws Exception\InvalidRoleDataException
     * @return array
     */
    public function parseRoleCode($roleCode)
    {
        if (! preg_match('/^B-(\d{5,5})-([A-Z-]+)$/', $roleCode, $matches)) {
            throw new Exception\InvalidRoleDataException(sprintf("Wrong role code format '%s'", $roleCode));
        }
        
        return array(
            'department_code' => $matches[1],
            'role_name' => $matches[2]
        );
    }


    /**
     * Returns only the roles for the required department.
     * 
     * @param array $roles
     * @param string $departmentCode
     * @return array
     */
    public function filterRolesByDepartmentCode(array $roles, $departmentCode = null)
    {
        if (null === $departmentCode) {
            $departmentCode = $this->getDepartmentCode();
        }
        
        $filteredRoles = array();
        foreach ($roles as $roleInfo) {
            if ($departmentCode === $roleInfo['department_code']) {
                $filteredRoles[] = $roleInfo;
            }
        }
        
        return $filteredRoles;
    }


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


    /**
     * Extracts the role code from the complex role string. The expected value is in the format:
     * 
     * B-<department_code>-<role_name>
     * 
     * where:
     * - department_code is a 5 digit numeric string
     * - role_name is an upper case string that may contain dashes
     * 
     * Example:
     * B-13000-ZAMESTNANEC-ADAKEMICKY
     * 
     * @param unknown $roleComplexValue
     * @throws Exception\InvalidRoleDataException
     * @return NULL|Ambigous <>
     */
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


    /**
     * Returns the value delimiter which separates the raw role strings.
     * 
     * @return string
     */
    protected function getValueDelimiter()
    {
        return $this->options->get(self::OPT_VALUE_DELIMITER);
    }


    /**
     * Returns the field delimiter which separates fields within a raw role string.
     * 
     * @return string
     */
    protected function getFieldDelimiter()
    {
        return $this->options->get(self::OPT_FIELD_DELIMITER);
    }


    /**
     * Returns the code of the department to extract roles for.
     * 
     * @throws MissingOptionException
     * @return string
     */
    protected function getDepartmentCode()
    {
        $departmentCode = $this->options->get(self::OPT_DEPARTMENT_CODE);
        if (null === $departmentCode) {
            throw new MissingOptionException(sprintf("Missing required option '%s'", self::OPT_DEPARTMENT_CODE));
        }
        
        return $departmentCode;
    }


    /**
     * Returns the map which assigns local role names to generic roles.
     * 
     * @return array
     */
    protected function getRoleMap()
    {
        return $this->options->get(self::OPT_ROLE_MAP, array());
    }
}