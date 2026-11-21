import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
 * @see routes/web.php:18
 * @route '/producto/{slug}'
 */
export const clean = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: clean.url(args, options),
    method: 'get',
})

clean.definition = {
    methods: ["get","head"],
    url: '/producto/{slug}',
} satisfies RouteDefinition<["get","head"]>

/**
 * @see routes/web.php:18
 * @route '/producto/{slug}'
 */
clean.url = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { slug: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    slug: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        slug: args.slug,
                }

    return clean.definition.url
            .replace('{slug}', parsedArgs.slug.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
 * @see routes/web.php:18
 * @route '/producto/{slug}'
 */
clean.get = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: clean.url(args, options),
    method: 'get',
})
/**
 * @see routes/web.php:18
 * @route '/producto/{slug}'
 */
clean.head = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: clean.url(args, options),
    method: 'head',
})

    /**
 * @see routes/web.php:18
 * @route '/producto/{slug}'
 */
    const cleanForm = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: clean.url(args, options),
        method: 'get',
    })

            /**
 * @see routes/web.php:18
 * @route '/producto/{slug}'
 */
        cleanForm.get = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: clean.url(args, options),
            method: 'get',
        })
            /**
 * @see routes/web.php:18
 * @route '/producto/{slug}'
 */
        cleanForm.head = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: clean.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    clean.form = cleanForm
const producto = {
    clean: Object.assign(clean, clean),
}

export default producto