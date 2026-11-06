import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\LibroReclamacionesController::dominio
 * @see app/Http/Controllers/LibroReclamacionesController.php:19
 * @route '/{dominio}/libro-de-reclamaciones'
 */
export const dominio = (args: { dominio: string | number } | [dominio: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: dominio.url(args, options),
    method: 'post',
})

dominio.definition = {
    methods: ["post"],
    url: '/{dominio}/libro-de-reclamaciones',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\LibroReclamacionesController::dominio
 * @see app/Http/Controllers/LibroReclamacionesController.php:19
 * @route '/{dominio}/libro-de-reclamaciones'
 */
dominio.url = (args: { dominio: string | number } | [dominio: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return dominio.definition.url
            .replace('{dominio}', parsedArgs.dominio.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LibroReclamacionesController::dominio
 * @see app/Http/Controllers/LibroReclamacionesController.php:19
 * @route '/{dominio}/libro-de-reclamaciones'
 */
dominio.post = (args: { dominio: string | number } | [dominio: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: dominio.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\LibroReclamacionesController::dominio
 * @see app/Http/Controllers/LibroReclamacionesController.php:19
 * @route '/{dominio}/libro-de-reclamaciones'
 */
    const dominioForm = (args: { dominio: string | number } | [dominio: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: dominio.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\LibroReclamacionesController::dominio
 * @see app/Http/Controllers/LibroReclamacionesController.php:19
 * @route '/{dominio}/libro-de-reclamaciones'
 */
        dominioForm.post = (args: { dominio: string | number } | [dominio: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: dominio.url(args, options),
            method: 'post',
        })
    
    dominio.form = dominioForm
/**
* @see \App\Http\Controllers\LibroReclamacionesController::slug
 * @see app/Http/Controllers/LibroReclamacionesController.php:19
 * @route '/{param1}/{param2}/libro-de-reclamaciones'
 */
export const slug = (args: { param1: string | number, param2: string | number } | [param1: string | number, param2: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: slug.url(args, options),
    method: 'post',
})

slug.definition = {
    methods: ["post"],
    url: '/{param1}/{param2}/libro-de-reclamaciones',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\LibroReclamacionesController::slug
 * @see app/Http/Controllers/LibroReclamacionesController.php:19
 * @route '/{param1}/{param2}/libro-de-reclamaciones'
 */
slug.url = (args: { param1: string | number, param2: string | number } | [param1: string | number, param2: string | number ], options?: RouteQueryOptions) => {
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

    return slug.definition.url
            .replace('{param1}', parsedArgs.param1.toString())
            .replace('{param2}', parsedArgs.param2.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LibroReclamacionesController::slug
 * @see app/Http/Controllers/LibroReclamacionesController.php:19
 * @route '/{param1}/{param2}/libro-de-reclamaciones'
 */
slug.post = (args: { param1: string | number, param2: string | number } | [param1: string | number, param2: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: slug.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\LibroReclamacionesController::slug
 * @see app/Http/Controllers/LibroReclamacionesController.php:19
 * @route '/{param1}/{param2}/libro-de-reclamaciones'
 */
    const slugForm = (args: { param1: string | number, param2: string | number } | [param1: string | number, param2: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: slug.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\LibroReclamacionesController::slug
 * @see app/Http/Controllers/LibroReclamacionesController.php:19
 * @route '/{param1}/{param2}/libro-de-reclamaciones'
 */
        slugForm.post = (args: { param1: string | number, param2: string | number } | [param1: string | number, param2: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: slug.url(args, options),
            method: 'post',
        })
    
    slug.form = slugForm
const store = {
    dominio: Object.assign(dominio, dominio),
slug: Object.assign(slug, slug),
}

export default store