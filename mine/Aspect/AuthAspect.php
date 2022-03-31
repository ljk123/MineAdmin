<?php
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

declare(strict_types=1);
namespace Mine\Aspect;

use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Di\Exception\Exception;
use Mine\Annotation\Auth;
use Mine\Exception\TokenException;
use Mine\Helper\LoginUser;
use Mine\MineRequest;

/**
 * Class AuthAspect
 * @package Mine\Aspect
 */
#[Aspect]
class AuthAspect extends AbstractAspect
{

    public $annotations = [
        Auth::class
    ];

    /**
     * @var LoginUser
     */
    protected $loginUser;

    public function __construct(LoginUser $loginUser)
    {
        $this->loginUser = $loginUser;
    }

    /**
     * @param ProceedingJoinPoint $proceedingJoinPoint
     * @return mixed
     * @throws Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $request = container()->get(MineRequest::class);
        if ($request->getMethod() != 'GET') {
            throw new MineException('为了正常运行，演示环境禁止该操作，如需要请下载部署体验');
        }

        if ($this->loginUser->check()) {
            return $proceedingJoinPoint->process();
        }
        throw new TokenException(t('jwt.validate_fail'));
    }
}