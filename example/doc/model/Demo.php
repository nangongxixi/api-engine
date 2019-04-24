<?php

namespace wxApi\doc;

/**
 * @OAS\Schema(
 *   schema="NewPet",
 *   type="object",
 *   required={"name"}
 * )
 */
class Demo
{
    public $id;
    /**
     * @OAS\Property(type="string")
     */
    public $name;
    /**
     * @OAS\Property(type="string")
     */
    public $tag;
}

// allOf �������� NewPet����
// �������� id

/**
 *  @OAS\Schema(
 *   schema="Pet",
 *   type="object",
 *   allOf={
 *       @OAS\Schema(ref="#/components/schemas/NewPet"),
 *       @OAS\Schema(
 *           required={"id"},
 *           @OAS\Property(property="id", format="int64", type="integer")
 *       )
 *   }
 * )
 */