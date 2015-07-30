<?php namespace Riari\Forum\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Riari\Forum\Forum;
use Riari\Forum\Http\Config\API\Error;

class CheckPermissions
{
	/**
	 * The parameters to pass to the permission checker.
	 *
	 * @var array
	 */
	protected $parameters = ['category', 'post', 'thread'];

	/**
	 * Handle an incoming request.
	 *
	 * @param  Request  $request
	 * @param  Closure  $next
	 * @return mixed
	 */
	public function handle(Request $request, Closure $next)
	{
		$route = $request->route();

		if (!Forum::permitted(
			$route->getName(),
			$request->all() + $route->parameters(),
			auth()->user()
		)) {
			if ('Riari\Forum\Http\Controllers\API\V1' == $route->getAction()['namespace']) {
				return response()->json([
					'error' => 'Authenticated user does not have permission to access this resource.',
					'code' 	=> Error::NOT_AUTHORISED
				], 403);
			}

			abort(403);
		}

		// Permission is granted; continue
		return $next($request);
	}
}
