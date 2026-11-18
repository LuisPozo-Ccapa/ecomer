import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\DeployWebhookController::handle
 * @see app/Http/Controllers/DeployWebhookController.php:17
 * @route '/api/deploy/run-tasks'
 */
export const handle = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: handle.url(options),
    method: 'post',
})

handle.definition = {
    methods: ["post"],
    url: '/api/deploy/run-tasks',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DeployWebhookController::handle
 * @see app/Http/Controllers/DeployWebhookController.php:17
 * @route '/api/deploy/run-tasks'
 */
handle.url = (options?: RouteQueryOptions) => {
    return handle.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DeployWebhookController::handle
 * @see app/Http/Controllers/DeployWebhookController.php:17
 * @route '/api/deploy/run-tasks'
 */
handle.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: handle.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\DeployWebhookController::handle
 * @see app/Http/Controllers/DeployWebhookController.php:17
 * @route '/api/deploy/run-tasks'
 */
    const handleForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: handle.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\DeployWebhookController::handle
 * @see app/Http/Controllers/DeployWebhookController.php:17
 * @route '/api/deploy/run-tasks'
 */
        handleForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: handle.url(options),
            method: 'post',
        })
    
    handle.form = handleForm
const DeployWebhookController = { handle }

export default DeployWebhookController