<?php

namespace ModularityResourceBooking\Helper;

abstract class ErrorHandler
{
    /**
     * Undocumented variable
     *
     * @var [type]
     */
    protected $error;

    /**
     * Undocumented function
     *
     * @return void
     */
    public function getErrors()
    {
        return $this->error;
    }

    /**
     * Undocumented function
     *
     * @param [type] $error
     * @return void
     */
    public function setErrorObject($error)
    {
        if (is_object($error) && get_class($error) === 'WP_Error') {
            $this->error = $error;
            return true;
        }

        return false;
    }

    /**
     * Undocumented function
     *
     * @param [type] $error
     * @param [type] $message
     * @param boolean $return
     * @return void
     */
    protected function addError($error, $message, $return = true)
    {
        if (!is_object($this->error) || get_class($this->error) !== 'WP_Error') {
            $this->error = new \WP_Error();
        }

        $objectVars = get_object_vars($this);
        unset($objectVars['error']);
        $this->error->add_data($objectVars, get_class($this));

        $this->error->add(get_class($this) . '::' . $error, $message);

        if ($return) {
            return new \WP_Error(get_class($this) . '::' . $error, $message);
        }
    }
}
