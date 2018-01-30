<?php

namespace Rebrandly\Service;

use Rebrandly\Model\Script as ScriptModel;
use Rebrandly\Model\Link as LinkModel;
use Rebrandly\Service\Http;

/**
 * Script Service
 *
 * Handles API requests for all Script related actions
 */
class Script
{
    /**
     * @var array Lists of which fields are required for particular operations
     *    Used for validation of script models before submission to the API
     */
    const REQUIREDFIELDS = [
        'create' => ['name' ,'value'],
        'update' => ['name' ,'value'],
    ];

    /**
     * @var Http $http HTTP helper class shared by all Rebrandly SDK services
     */
    private $http;

    public function __construct($apiKey)
    {
        $this->http = new Http($apiKey);
    }


    /**
     * Ensures all required fields for the requested action exist on a $script
     *
     * While the script model includes its own validation of fields on assignment
     * and hence we can trust that any set data is of the correct type etc, the
     * script model doesn't know about the API's requirements.
     *
     * @param string $action The action being performed, used to look up which
     *    fields are required
     *
     * @param array $scriptArray Array describing the script being validated
     */
    private function validate($action, $scriptArray)
    {
        $missing = array_flip(array_diff_key(array_flip(self::REQUIREDFIELDS[$action]), $scriptArray));

        if (count($missing) > 0) {
            throw new \InvalidArgumentException("Missing required fields: " . join(' ', $missing));
        }
    }

    /**
     * Shorthand to shorten a script given nothing but the destination
     *
     * @param string $destination The target URL that the shortened script should
     *    resolve to.
     *
     * @return ScriptModel $script A script populated with the response from the
     *    Rebrandly API.
     */
    public function quickCreate($name, $value)
    {
        $script = new ScriptModel();
        $script->setName($name);
        $script->setValue($value);

        return $this->fullCreate($script);
    }

    /**
     * Creates a script given ScriptModel with any desired details
     *
     * @param array $script Any fields the user wishes to set on the script before
     *    creation
     *
     * @return ScriptModel $script A script populated with the response from the
     *    Rebrandly API.
     */
    public function fullCreate(ScriptModel $script)
    {
        $target = 'scripts';
        $scriptArray = $script->export();
        $this->validate('create', $scriptArray);

        $response = $this->http->post($target, $scriptArray);

        $script = ScriptModel::import($response);

        return $script;
    }

    /**
     * Updates a script given all existing and any new data
     *
     * @param ScriptModel $script A new script to update with
     *
     * @return ScriptModel $script An updated script as returned from the API
     */
    public function update(ScriptModel $script)
    {
        $target = 'scripts/' . $script->getId();
        $scriptArray = $script->export();
        $this->validate('update', $scriptArray);

        $response = $this->http->post($target, $scriptArray);

        $script = ScriptModel::import($response);

        return $script;
    }

    /**
     * Gets full details of a single script given its ID
     *
     * @param string $scriptId the ID of the script, as provided on creation by
     *    Rebrandly
     *
     * @return scriptModel $script A populated script as returned from the API
     */
    public function getOne($scriptId)
    {
        $target = 'scripts/' . $scriptId;

        $response = $this->http->get($target);

        $script = ScriptModel::import($response);

        return $script;
    }

    /**
     * Deletes a script
     *
     * @param ScriptModel $script The script to delete
     *
     * TODO: Check what this response actually is
     * @return array $response Whatever response the API gives us.
     */
    public function delete(ScriptModel $script)
    {
        $scriptId = $script->getId();

        $response = $this->deleteById($scriptId);

        return $response;
    }

    /**
     * Alternate means to call delete, accepting a script ID rather than a full
     * script model.
     *
     * @param integer $scriptId The script ID to delete
     *
     * TODO: Check what this response actually is
     * @return array $response Whatever response the API gives us.
     */
    public function deleteById($scriptId)
    {
        $target = 'scripts/' . $scriptId;

        $response = $this->http->delete($target);

        return $response;
    }


    /**
     * Attaching a script to link.
     *
     * @param LinkModel $link 
     * @param ScriptModel $script 
     *
     * TODO: Check what this response actually is
     * @return array $response Whatever response the API gives us.
     */
    public function attachToLink(LinkModel $link, ScriptModel $script)
    {
        $target = 'links/' . $link->getId() . '/scripts/' . $script->getId();

        $response = $this->http->post($target);

        return $response;
    }

    /**
     * Detaching a script from link.
     *
     * @param LinkModel $link 
     * @param ScriptModel $script 
     *
     * TODO: Check what this response actually is
     * @return array $response Whatever response the API gives us.
     */
    public function detachToLink(LinkModel $link, ScriptModel $script)
    {
        $target = 'links/' . $link->getId() . '/scripts/' . $script->getId();

        $response = $this->http->delete($target);

        return $response;
    }

     /**
     * Search for scripts meeting some criteria, with sorting controls
     *
     * @param array $filters A list of parameters to filter and sort by
     *
     * @return ScriptModel[] $scripts A list of scripts that meet the given criteria
     */
    public function search($filters = [])
    {
        $target = 'scripts/';

        $response = $this->http->get($target, $filters);

        $scripts = [];
        foreach ($response as $scriptArray) {
            $script = ScriptModel::import($scriptArray);
            array_push($scripts, $script);
        }

        return $scripts;
    }

    /**
     * Counts scripts, with optional filters
     *
     * @param array $filters Fields to restrict the count by
     *
     * @return array $scripts A list of scripts matching the given criteria
     */
    public function count($filters = [])
    {
        $target = 'scripts/count';

        $count = $this->http->get($target, $filters);

        return $count;
    }
}
