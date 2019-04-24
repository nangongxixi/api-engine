<?php

/**
 * @OAS\Schema(
 *   schema="Photo", type="object", required={"id", "title", "image"},
 *   @OAS\Property(property="id", format="int64", type="integer", description="自增id"),
 *   @OAS\Property(property="title", type="string", description="图相标题"),
 *   @OAS\Property(property="image", type="string", description="图片路径"),
 *   @OAS\Property(property="linkImg", ref="#/components/schemas/PhotoImage"),
 *   @OAS\Property(
 *     property="images",
 *     type="array",
 *     @OAS\Items(ref="#/components/schemas/PhotoImage")
 *   ),
 * )
 */

/**
 * @OAS\Schema(
 *   schema="PhotoImage", type="object", required={"id", "image"},
 *   @OAS\Property(property="id", format="int64", type="integer", description="自增id"),
 *   @OAS\Property(property="image", type="string", description="图片路径")
 * )
 */

/**
 * @OAS\Get(
 *     path="/photo/search",
 *     description="效果图搜索",
 *
 *     @OAS\Parameter(
 *         name="page", in="query", description="分页参数", example=1, required=false,
 *         @OAS\Schema(type="integer", format="int32"),
 *     ),
 *     @OAS\Parameter(
 *         name="style", in="query", description="风格类别id", required=false,
 *         @OAS\Schema(type="integer",format="int32")
 *     ),
 *
 *     @OAS\Response(
 *         response=200, description="pet response",
 *
 *         @OAS\MediaType(
 *             mediaType="application/json",
 *             @OAS\Schema(
 *                 type="object", required={"code", "items"},
 *                 @OAS\Property(property="code", type="integer", description="状态码"),
 *                 @OAS\Property(
 *                    property="items", type="array", description="效果图集合",
 *                    @OAS\Items(ref="#/components/schemas/Photo")
 *                 )
 *             ),
 *         ),
 *
 *         @OAS\Response(
 *             response=200, description="pet response",
 *             @OAS\MediaType(
 *                 mediaType="application/json",
 *                 @OAS\Schema(
 *                    type="array",
 *                    @OAS\Items(ref="#/components/schemas/Photo")
 *                 ),
 *             )
 *         )
 *     )
 * )
 */

/**
 * @OAS\Get(
 *     path="/photo/info",
 *     description="效果图详情", operationId="findPets",
 *     @OAS\Parameter(
 *         name="id", in="query", description="分页参数", required=true, style="form",
 *         @OAS\Schema(type="integer", format="int32"),
 *     ),
 *     @OAS\Response(
 *         response=200, description="pet response",
 *         @OAS\MediaType(
 *             mediaType="application/json",
 *             @OAS\Schema(ref="#/components/schemas/Photo")
 *         )
 *     )
 * )
 */