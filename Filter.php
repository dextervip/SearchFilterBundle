<?php

namespace Citrax\Bundle\SearchFilterBundle;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Description of Filter
 *
 * @author Rafael
 */
class Filter {
    
    /* @var $session \Symfony\Component\HttpFoundation\Session */
    private $session;

    /* @var $request \Symfony\Component\HttpFoundation\Request */
    private $request;

    private $sessionPrefix;
    private $filterParameters = array();
    private $criteria = array();

    public function __construct(Session $session, $sessionPrefix) {
        $this->session = $session;
        $this->sessionPrefix = $sessionPrefix;
        $this->loadSession();
    }

    public function getFilterParameters() {
        return $this->filterParameters;
    }

    public function setFilterParameters(array $filterParameters) {
        $this->filterParameters = $filterParameters;
        return $this;
    }

    /**
     * Parses filter criterias from request
     * @param Request $request
     */
    public function parseCriteria(Request $request) {
        
        if($request->get('clear') == 'clear'){
            $this->clearCriteria();
            return;
        }
        
        foreach ($this->filterParameters as $name) {
            $param = $request->get($name);
            if ($param !== null) {
                if (empty($param) == true) {
                    if ($this->hasCriteria($name) == true) {
                        $this->removeCriteria($name);
                    }
                }else{
                    $this->addCriteria($name, $param);
                }
            }
            
        }
    }

    public function addCriteria($name, $value) {
        $this->criteria[$name] = $value;
        $this->saveInSession();
    }

    public function removeCriteria($name) {
        unset($this->criteria[$name]);
        $this->saveInSession();
    }

    public function hasCriteria($name) {
        return isset($this->criteria[$name]);
    }

    public function clearCriteria() {
        $this->criteria = array();
        $this->saveInSession();
    }

    public function getCriteria() {
        return $this->criteria;
    }

    private function saveInSession() {
        $this->session->set($this->sessionPrefix . 'criteria', $this->criteria);
    }

    private function loadSession() {
        if ($this->session->has($this->sessionPrefix . 'criteria') == true) {
            $this->criteria = $this->session->get($this->sessionPrefix . 'criteria');
        }
    }
    
    public function getParameterValue($name) {
        if(isset($this->criteria[$name]) == true ){
            return $this->criteria[$name];
        }
        return '';
    }

}

