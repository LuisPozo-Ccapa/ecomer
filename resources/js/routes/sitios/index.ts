import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\SitePageController::seccion
 * @see app/Http/Controllers/SitePageController.php:38
 * @route '/{seccion}'
 */
export const seccion = (args: { seccion: string | number } | [seccion: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: seccion.url(args, options),
    method: 'get',
})

seccion.definition = {
    methods: ["get","head"],
    url: '/{seccion}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SitePageController::seccion
 * @see app/Http/Controllers/SitePageController.php:38
 * @route '/{seccion}'
 */
seccion.url = (args: { seccion: string | number } | [seccion: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { seccion: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    seccion: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        seccion: args.seccion,
                }

    return seccion.definition.url
            .replace('{seccion}', parsedArgs.seccion.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SitePageController::seccion
 * @see app/Http/Controllers/SitePageController.php:38
 * @route '/{seccion}'
 */
seccion.get = (args: { seccion: string | number } | [seccion: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: seccion.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\SitePageController::seccion
 * @see app/Http/Controllers/SitePageController.php:38
 * @route '/{seccion}'
 */
seccion.head = (args: { seccion: string | number } | [seccion: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: seccion.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\SitePageController::seccion
 * @see app/Http/Controllers/SitePageController.php:38
 * @route '/{seccion}'
 */
    const seccionForm = (args: { seccion: string | number } | [seccion: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: seccion.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\SitePageController::seccion
 * @see app/Http/Controllers/SitePageController.php:38
 * @route '/{seccion}'
 */
        seccionForm.get = (args: { seccion: string | number } | [seccion: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: seccion.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\SitePageController::seccion
 * @see app/Http/Controllers/SitePageController.php:38
 * @route '/{seccion}'
 */
        seccionForm.head = (args: { seccion: string | number } | [seccion: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: seccion.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    seccion.form = seccionForm
/**
* @see \App\Http\Controllers\SitePageController::show
 * @see app/Http/Controllers/SitePageController.php:38
 * @route '/{dominio}/{seccion}'
 */
export const show = (args: { dominio: string | number, seccion: string | number } | [dominio: string | number, seccion: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/{dominio}/{seccion}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SitePageController::show
 * @see app/Http/Controllers/SitePageController.php:38
 * @route '/{dominio}/{seccion}'
 */
show.url = (args: { dominio: string | number, seccion: string | number } | [dominio: string | number, seccion: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
                    dominio: args[0],
                    seccion: args[1],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        dominio: args.dominio,
                                seccion: args.seccion,
                }

    return show.definition.url
            .replace('{dominio}', parsedArgs.dominio.toString())
            .replace('{seccion}', parsedArgs.seccion.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SitePageController::show
 * @see app/Http/Controllers/SitePageController.php:38
 * @route '/{dominio}/{seccion}'
 */
show.get = (args: { dominio: string | number, seccion: string | number } | [dominio: string | number, seccion: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\SitePageController::show
 * @see app/Http/Controllers/SitePageController.php:38
 * @route '/{dominio}/{seccion}'
 */
show.head = (args: { dominio: string | number, seccion: string | number } | [dominio: string | number, seccion: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\SitePageController::show
 * @see app/Http/Controllers/SitePageController.php:38
 * @route '/{dominio}/{seccion}'
 */
    const showForm = (args: { dominio: string | number, seccion: string | number } | [dominio: string | number, seccion: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\SitePageController::show
 * @see app/Http/Controllers/SitePageController.php:38
 * @route '/{dominio}/{seccion}'
 */
        showForm.get = (args: { dominio: string | number, seccion: string | number } | [dominio: string | number, seccion: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\SitePageController::show
 * @see app/Http/Controllers/SitePageController.php:38
 * @route '/{dominio}/{seccion}'
 */
        showForm.head = (args: { dominio: string | number, seccion: string | number } | [dominio: string | number, seccion: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    show.form = showForm
/**
* @see \App\Http\Controllers\SitePageController::show3
 * @see app/Http/Controllers/SitePageController.php:38
 * @route '/{param1}/{param2}/{param3}'
 */
export const show3 = (args: { param1: string | number, param2: string | number, param3: string | number } | [param1: string | number, param2: string | number, param3: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show3.url(args, options),
    method: 'get',
})

show3.definition = {
    methods: ["get","head"],
    url: '/{param1}/{param2}/{param3}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SitePageController::show3
 * @see app/Http/Controllers/SitePageController.php:38
 * @route '/{param1}/{param2}/{param3}'
 */
show3.url = (args: { param1: string | number, param2: string | number, param3: string | number } | [param1: string | number, param2: string | number, param3: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
                    param1: args[0],
                    param2: args[1],
                    param3: args[2],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        param1: args.param1,
                                param2: args.param2,
                                param3: args.param3,
                }

    return show3.definition.url
            .replace('{param1}', parsedArgs.param1.toString())
            .replace('{param2}', parsedArgs.param2.toString())
            .replace('{param3}', parsedArgs.param3.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SitePageController::show3
 * @see app/Http/Controllers/SitePageController.php:38
 * @route '/{param1}/{param2}/{param3}'
 */
show3.get = (args: { param1: string | number, param2: string | number, param3: string | number } | [param1: string | number, param2: string | number, param3: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show3.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\SitePageController::show3
 * @see app/Http/Controllers/SitePageController.php:38
 * @route '/{param1}/{param2}/{param3}'
 */
show3.head = (args: { param1: string | number, param2: string | number, param3: string | number } | [param1: string | number, param2: string | number, param3: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show3.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\SitePageController::show3
 * @see app/Http/Controllers/SitePageController.php:38
 * @route '/{param1}/{param2}/{param3}'
 */
    const show3Form = (args: { param1: string | number, param2: string | number, param3: string | number } | [param1: string | number, param2: string | number, param3: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show3.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\SitePageController::show3
 * @see app/Http/Controllers/SitePageController.php:38
 * @route '/{param1}/{param2}/{param3}'
 */
        show3Form.get = (args: { param1: string | number, param2: string | number, param3: string | number } | [param1: string | number, param2: string | number, param3: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show3.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\SitePageController::show3
 * @see app/Http/Controllers/SitePageController.php:38
 * @route '/{param1}/{param2}/{param3}'
 */
        show3Form.head = (args: { param1: string | number, param2: string | number, param3: string | number } | [param1: string | number, param2: string | number, param3: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show3.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    show3.form = show3Form
const sitios = {
    seccion: Object.assign(seccion, seccion),
show: Object.assign(show, show),
show3: Object.assign(show3, show3),
}

export default sitios