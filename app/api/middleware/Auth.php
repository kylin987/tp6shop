<?php
declare (strict_types = 1);

namespace app\api\middleware;

class Auth
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        $accessToken = $request->header('access-token');
        if (empty($accessToken)) {
            return show(config('status.not_login'),'未登录');
        }
        $userInfo = cache(config('redis.token_pre').$accessToken);
        if (!$userInfo || empty($userInfo['id']) || empty($userInfo['username'])) {
            return show(config('status.not_login'),'未登录');
        }

        return $next($request);
    }
}
