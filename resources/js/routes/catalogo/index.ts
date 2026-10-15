import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\SitePageController::descargar
* @see app/Http/Controllers/SitePageController.php:546
* @route '/catalogo/descargar-pdf'
*/
export const descargar = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: descargar.url(options),
    method: 'get',
})

descargar.definition = {
    methods: ["get","head"],
    url: '/catalogo/descargar-pdf',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SitePageController::descargar
* @see app/Http/Controllers/SitePageController.php:546
* @route '/catalogo/descargar-pdf'
*/
descargar.url = (options?: RouteQueryOptions) => {
    return descargar.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SitePageController::descargar
* @see app/Http/Controllers/SitePageController.php:546
* @route '/catalogo/descargar-pdf'
*/
descargar.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: descargar.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SitePageController::descargar
* @see app/Http/Controllers/SitePageController.php:546
* @route '/catalogo/descargar-pdf'
*/
descargar.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: descargar.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SitePageController::descargar
* @see app/Http/Controllers/SitePageController.php:546
* @route '/catalogo/descargar-pdf'
*/
const descargarForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: descargar.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SitePageController::descargar
* @see app/Http/Controllers/SitePageController.php:546
* @route '/catalogo/descargar-pdf'
*/
descargarForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: descargar.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SitePageController::descargar
* @see app/Http/Controllers/SitePageController.php:546
* @route '/catalogo/descargar-pdf'
*/
descargarForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: descargar.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

descargar.form = descargarForm

/**
* @see \App\Http\Controllers\SitePageController::descargar1
* @see app/Http/Controllers/SitePageController.php:546
* @route '/{dominio}/catalogo/descargar-pdf'
*/
export const descargar1 = (args: { dominio: string | number } | [dominio: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: descargar1.url(args, options),
    method: 'get',
})

descargar1.definition = {
    methods: ["get","head"],
    url: '/{dominio}/catalogo/descargar-pdf',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SitePageController::descargar1
* @see app/Http/Controllers/SitePageController.php:546
* @route '/{dominio}/catalogo/descargar-pdf'
*/
descargar1.url = (args: { dominio: string | number } | [dominio: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { dominio: args }
    }

    if (Array.isArray(args)) {
        args = {
            dominio: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        dominio: args.dominio,
    }

    return descargar1.definition.url
            .replace('{dominio}', parsedArgs.dominio.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SitePageController::descargar1
* @see app/Http/Controllers/SitePageController.php:546
* @route '/{dominio}/catalogo/descargar-pdf'
*/
descargar1.get = (args: { dominio: string | number } | [dominio: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: descargar1.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SitePageController::descargar1
* @see app/Http/Controllers/SitePageController.php:546
* @route '/{dominio}/catalogo/descargar-pdf'
*/
descargar1.head = (args: { dominio: string | number } | [dominio: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: descargar1.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SitePageController::descargar1
* @see app/Http/Controllers/SitePageController.php:546
* @route '/{dominio}/catalogo/descargar-pdf'
*/
const descargar1Form = (args: { dominio: string | number } | [dominio: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: descargar1.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SitePageController::descargar1
* @see app/Http/Controllers/SitePageController.php:546
* @route '/{dominio}/catalogo/descargar-pdf'
*/
descargar1Form.get = (args: { dominio: string | number } | [dominio: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: descargar1.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SitePageController::descargar1
* @see app/Http/Controllers/SitePageController.php:546
* @route '/{dominio}/catalogo/descargar-pdf'
*/
descargar1Form.head = (args: { dominio: string | number } | [dominio: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: descargar1.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

descargar1.form = descargar1Form

/**
* @see \App\Http\Controllers\SitePageController::descargar2
* @see app/Http/Controllers/SitePageController.php:546
* @route '/{param1}/{param2}/catalogo/descargar-pdf'
*/
export const descargar2 = (args: { param1: string | number, param2: string | number } | [param1: string | number, param2: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: descargar2.url(args, options),
    method: 'get',
})

descargar2.definition = {
    methods: ["get","head"],
    url: '/{param1}/{param2}/catalogo/descargar-pdf',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SitePageController::descargar2
* @see app/Http/Controllers/SitePageController.php:546
* @route '/{param1}/{param2}/catalogo/descargar-pdf'
*/
descargar2.url = (args: { param1: string | number, param2: string | number } | [param1: string | number, param2: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            param1: args[0],
            param2: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        param1: args.param1,
        param2: args.param2,
    }

    return descargar2.definition.url
            .replace('{param1}', parsedArgs.param1.toString())
            .replace('{param2}', parsedArgs.param2.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SitePageController::descargar2
* @see app/Http/Controllers/SitePageController.php:546
* @route '/{param1}/{param2}/catalogo/descargar-pdf'
*/
descargar2.get = (args: { param1: string | number, param2: string | number } | [param1: string | number, param2: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: descargar2.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SitePageController::descargar2
* @see app/Http/Controllers/SitePageController.php:546
* @route '/{param1}/{param2}/catalogo/descargar-pdf'
*/
descargar2.head = (args: { param1: string | number, param2: string | number } | [param1: string | number, param2: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: descargar2.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SitePageController::descargar2
* @see app/Http/Controllers/SitePageController.php:546
* @route '/{param1}/{param2}/catalogo/descargar-pdf'
*/
const descargar2Form = (args: { param1: string | number, param2: string | number } | [param1: string | number, param2: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: descargar2.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SitePageController::descargar2
* @see app/Http/Controllers/SitePageController.php:546
* @route '/{param1}/{param2}/catalogo/descargar-pdf'
*/
descargar2Form.get = (args: { param1: string | number, param2: string | number } | [param1: string | number, param2: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: descargar2.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SitePageController::descargar2
* @see app/Http/Controllers/SitePageController.php:546
* @route '/{param1}/{param2}/catalogo/descargar-pdf'
*/
descargar2Form.head = (args: { param1: string | number, param2: string | number } | [param1: string | number, param2: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: descargar2.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

descargar2.form = descargar2Form

const catalogo = {
    descargar: Object.assign(descargar, descargar),
    descargar1: Object.assign(descargar1, descargar1),
    descargar2: Object.assign(descargar2, descargar2),
}

export default catalogo