import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\v1\TikTokApiController::oembed
 * @see app/Http/Controllers/Api/v1/TikTokApiController.php:18
 * @route '/api/v1/tiktok-oembed'
 */
export const oembed = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: oembed.url(options),
    method: 'get',
})

oembed.definition = {
    methods: ["get","head"],
    url: '/api/v1/tiktok-oembed',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\v1\TikTokApiController::oembed
 * @see app/Http/Controllers/Api/v1/TikTokApiController.php:18
 * @route '/api/v1/tiktok-oembed'
 */
oembed.url = (options?: RouteQueryOptions) => {
    return oembed.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\v1\TikTokApiController::oembed
 * @see app/Http/Controllers/Api/v1/TikTokApiController.php:18
 * @route '/api/v1/tiktok-oembed'
 */
oembed.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: oembed.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Api\v1\TikTokApiController::oembed
 * @see app/Http/Controllers/Api/v1/TikTokApiController.php:18
 * @route '/api/v1/tiktok-oembed'
 */
oembed.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: oembed.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Api\v1\TikTokApiController::oembed
 * @see app/Http/Controllers/Api/v1/TikTokApiController.php:18
 * @route '/api/v1/tiktok-oembed'
 */
    const oembedForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: oembed.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Api\v1\TikTokApiController::oembed
 * @see app/Http/Controllers/Api/v1/TikTokApiController.php:18
 * @route '/api/v1/tiktok-oembed'
 */
        oembedForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: oembed.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Api\v1\TikTokApiController::oembed
 * @see app/Http/Controllers/Api/v1/TikTokApiController.php:18
 * @route '/api/v1/tiktok-oembed'
 */
        oembedForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: oembed.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    oembed.form = oembedForm
const TikTokApiController = { oembed }

export default TikTokApiController