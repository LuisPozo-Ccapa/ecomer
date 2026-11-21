import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\RobotsController::txt
 * @see app/Http/Controllers/RobotsController.php:12
 * @route '/robots.txt'
 */
export const txt = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: txt.url(options),
    method: 'get',
})

txt.definition = {
    methods: ["get","head"],
    url: '/robots.txt',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\RobotsController::txt
 * @see app/Http/Controllers/RobotsController.php:12
 * @route '/robots.txt'
 */
txt.url = (options?: RouteQueryOptions) => {
    return txt.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\RobotsController::txt
 * @see app/Http/Controllers/RobotsController.php:12
 * @route '/robots.txt'
 */
txt.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: txt.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\RobotsController::txt
 * @see app/Http/Controllers/RobotsController.php:12
 * @route '/robots.txt'
 */
txt.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: txt.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\RobotsController::txt
 * @see app/Http/Controllers/RobotsController.php:12
 * @route '/robots.txt'
 */
    const txtForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: txt.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\RobotsController::txt
 * @see app/Http/Controllers/RobotsController.php:12
 * @route '/robots.txt'
 */
        txtForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: txt.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\RobotsController::txt
 * @see app/Http/Controllers/RobotsController.php:12
 * @route '/robots.txt'
 */
        txtForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: txt.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    txt.form = txtForm
const robots = {
    txt: Object.assign(txt, txt),
}

export default robots