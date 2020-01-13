<?php

namespace app\admin\middleware;

/**
 * Auth中间件
 */
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
        //前置中间件
        //如果是验证码控制器，直接过
        if(preg_match('/verify/', $request->pathinfo())){
            return $next($request);
        }
        //如果session_admin为空，并且不在登录页面，跳转到登录页面
        if (empty(session(config('admin.session_admin'))) && !preg_match('/login/', $request->pathinfo())) {
            return redirect(url('login/index'));
        }
        //如果session_admin不为空，并且在登录页面，跳转到后台主页
        if (!empty(session(config('admin.session_admin'))) && preg_match('/login/', $request->pathinfo())) {
            return redirect(url('index/index'));
        }

        return $next($request);
        //后置中间件
    }
}