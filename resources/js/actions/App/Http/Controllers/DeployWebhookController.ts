import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\DeployWebhookController::handle
 * @see app/Http/Controllers/DeployWebhookController.php:17
 * @route '/api/deploy/run-tasks'
 */
const handle505ca52e7c0809df6746db74a94e3ee1 = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: handle505ca52e7c0809df6746db74a94e3ee1.url(options),
    method: 'post',
})

handle505ca52e7c0809df6746db74a94e3ee1.definition = {
    methods: ["post"],
    url: '/api/deploy/run-tasks',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DeployWebhookController::handle
 * @see app/Http/Controllers/DeployWebhookController.php:17
 * @route '/api/deploy/run-tasks'
 */
handle505ca52e7c0809df6746db74a94e3ee1.url = (options?: RouteQueryOptions) => {
    return handle505ca52e7c0809df6746db74a94e3ee1.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DeployWebhookController::handle
 * @see app/Http/Controllers/DeployWebhookController.php:17
 * @route '/api/deploy/run-tasks'
 */
handle505ca52e7c0809df6746db74a94e3ee1.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: handle505ca52e7c0809df6746db74a94e3ee1.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\DeployWebhookController::handle
 * @see app/Http/Controllers/DeployWebhookController.php:17
 * @route '/api/deploy/run-tasks'
 */
    const handle505ca52e7c0809df6746db74a94e3ee1Form = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: handle505ca52e7c0809df6746db74a94e3ee1.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\DeployWebhookController::handle
 * @see app/Http/Controllers/DeployWebhookController.php:17
 * @route '/api/deploy/run-tasks'
 */
        handle505ca52e7c0809df6746db74a94e3ee1Form.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: handle505ca52e7c0809df6746db74a94e3ee1.url(options),
            method: 'post',
        })
    
    handle505ca52e7c0809df6746db74a94e3ee1.form = handle505ca52e7c0809df6746db74a94e3ee1Form
    /**
* @see \App\Http\Controllers\DeployWebhookController::handle
 * @see app/Http/Controllers/DeployWebhookController.php:17
 * @route '/deploy/run-tasks'
 */
const handle9fdb0a12dde668acb4920c0fac0f524a = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: handle9fdb0a12dde668acb4920c0fac0f524a.url(options),
    method: 'post',
})

handle9fdb0a12dde668acb4920c0fac0f524a.definition = {
    methods: ["post"],
    url: '/deploy/run-tasks',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DeployWebhookController::handle
 * @see app/Http/Controllers/DeployWebhookController.php:17
 * @route '/deploy/run-tasks'
 */
handle9fdb0a12dde668acb4920c0fac0f524a.url = (options?: RouteQueryOptions) => {
    return handle9fdb0a12dde668acb4920c0fac0f524a.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DeployWebhookController::handle
 * @see app/Http/Controllers/DeployWebhookController.php:17
 * @route '/deploy/run-tasks'
 */
handle9fdb0a12dde668acb4920c0fac0f524a.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: handle9fdb0a12dde668acb4920c0fac0f524a.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\DeployWebhookController::handle
 * @see app/Http/Controllers/DeployWebhookController.php:17
 * @route '/deploy/run-tasks'
 */
    const handle9fdb0a12dde668acb4920c0fac0f524aForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: handle9fdb0a12dde668acb4920c0fac0f524a.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\DeployWebhookController::handle
 * @see app/Http/Controllers/DeployWebhookController.php:17
 * @route '/deploy/run-tasks'
 */
        handle9fdb0a12dde668acb4920c0fac0f524aForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: handle9fdb0a12dde668acb4920c0fac0f524a.url(options),
            method: 'post',
        })
    
    handle9fdb0a12dde668acb4920c0fac0f524a.form = handle9fdb0a12dde668acb4920c0fac0f524aForm

/**
* Multiple routes resolve to \App\Http\Controllers\DeployWebhookController::handle, so this export is a
* dictionary keyed by URI rather than a callable, with the verbs prefixed where two routes share a URI.
* Call a specific route with `handle['<key>'](...)`, or import the route by name from your
* generated `routes/` directory.
*/
export const handle = {
    '/api/deploy/run-tasks': handle505ca52e7c0809df6746db74a94e3ee1,
    '/deploy/run-tasks': handle9fdb0a12dde668acb4920c0fac0f524a,
}

const DeployWebhookController = { handle }

export default DeployWebhookController