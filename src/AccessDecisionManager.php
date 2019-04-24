<?php

namespace j\apiSimple;

use j\apiSimple\event\AuthEvent;

/**
 * Class AccessDecisionManager
 * @package j\apiSimple
 */
class AccessDecisionManager {

    const ACCESS_GRANTED = 1;
    const ACCESS_ABSTAIN = 0;
    const ACCESS_DENIED = -1;

    public $allowIfAllAbstainDecisions = false;

    /**
     * @var callable[]
     */
    private $voters = [];

    /**
     * @param callable[] $voters
     */
    public function setVoters($voters){
        $this->voters = $voters;
    }

    /**
     * @param $voter
     */
    function addVoter($voter){
        $this->voters[] = $voter;
    }


    /**
     * @param AuthEvent $token
     * @param array $attributes
     * @param null $object
     * @return bool
     */
    public function decide($token, $attributes, $object = null)
    {
        foreach ($this->voters as $voter) {
            $result = call_user_func($voter, $token, $object, $attributes);
            switch ($result) {
                case self::ACCESS_GRANTED:
                    return true;

                case self::ACCESS_DENIED:
                    return false;

                default:
                    break;
            }
        }

        return $this->allowIfAllAbstainDecisions;
    }
}
