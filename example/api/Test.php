<?php
#Test.php created by stcer@jz at 2017/7/18
namespace j\apiSimple\example\api;

/**
 * @OA\Info(title="My First API", version="0.1")
 * @OA\Schema(required={"id", "name"})
 * Class Test
 * @package wxapp\api
 */
class Test extends Base{

    /**
     * @OA\Property()
     * @var string
     */
    public $apiName;

    /**
     * @OA\Property()
     * @var string
     */
    public $message;

    /**
     * @param $request
     * @return array
     * @OA\Get(
     *     path="/index.php?api=test",
     *     @OA\Response(
     *       response="200",
     *       description="An example resource"
     *    )
     * )
     */
    public function actionDefault($request){
        // TODO: Implement handle() method.
        return [
            'apiName' => 'Test gbkÖĞÎÄ',
            'message' => 'This is a test',
        ];
    }
}