import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\LibroReclamacionesController::store
 * @see app/Http/Controllers/LibroReclamacionesController.php:19
 * @route '/libro-de-reclamaciones'
 */
const store6e1c782efba9f15f83c3af01e6a8009a = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store6e1c782efba9f15f83c3af01e6a8009a.url(options),
    method: 'post',
})

store6e1c782efba9f15f83c3af01e6a8009a.definition = {
    methods: ["post"],
    url: '/libro-de-reclamaciones',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\LibroReclamacionesController::store
 * @see app/Http/Controllers/LibroReclamacionesController.php:19
 * @route '/libro-de-reclamaciones'
 */
store6e1c782efba9f15f83c3af01e6a8009a.url = (options?: RouteQueryOptions) => {
    return store6e1c782efba9f15f83c3af01e6a8009a.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\LibroReclamacionesController::store
 * @see app/Http/Controllers/LibroReclamacionesController.php:19
 * @route '/libro-de-reclamaciones'
 */
store6e1c782efba9f15f83c3af01e6a8009a.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store6e1c782efba9f15f83c3af01e6a8009a.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\LibroReclamacionesController::store
 * @see app/Http/Controllers/LibroReclamacionesController.php:19
 * @route '/libro-de-reclamaciones'
 */
    const store6e1c782efba9f15f83c3af01e6a8009aForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store6e1c782efba9f15f83c3af01e6a8009a.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\LibroReclamacionesController::store
 * @see app/Http/Controllers/LibroReclamacionesController.php:19
 * @route '/libro-de-reclamaciones'
 */
        store6e1c782efba9f15f83c3af01e6a8009aForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store6e1c782efba9f15f83c3af01e6a8009a.url(options),
            method: 'post',
        })
    
    store6e1c782efba9f15f83c3af01e6a8009a.form = store6e1c782efba9f15f83c3af01e6a8009aForm
    /**
* @see \App\Http\Controllers\LibroReclamacionesController::store
 * @see app/Http/Controllers/LibroReclamacionesController.php:19
 * @route '/{dominio}/libro-de-reclamaciones'
 */
const store1d3ba87ff951e65d2c2a1c72453353e6 = (args: { dominio: string | number } | [dominio: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store1d3ba87ff951e65d2c2a1c72453353e6.url(args, options),
    method: 'post',
})

store1d3ba87ff951e65d2c2a1c72453353e6.definition = {
    methods: ["post"],
    url: '/{dominio}/libro-de-reclamaciones',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\LibroReclamacionesController::store
 * @see app/Http/Controllers/LibroReclamacionesController.php:19
 * @route '/{dominio}/libro-de-reclamaciones'
 */
store1d3ba87ff951e65d2c2a1c72453353e6.url = (args: { dominio: string | number } | [dominio: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return store1d3ba87ff951e65d2c2a1c72453353e6.definition.url
            .replace('{dominio}', parsedArgs.dominio.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LibroReclamacionesController::store
 * @see app/Http/Controllers/LibroReclamacionesController.php:19
 * @route '/{dominio}/libro-de-reclamaciones'
 */
store1d3ba87ff951e65d2c2a1c72453353e6.post = (args: { dominio: string | number } | [dominio: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store1d3ba87ff951e65d2c2a1c72453353e6.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\LibroReclamacionesController::store
 * @see app/Http/Controllers/LibroReclamacionesController.php:19
 * @route '/{dominio}/libro-de-reclamaciones'
 */
    const store1d3ba87ff951e65d2c2a1c72453353e6Form = (args: { dominio: string | number } | [dominio: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store1d3ba87ff951e65d2c2a1c72453353e6.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\LibroReclamacionesController::store
 * @see app/Http/Controllers/LibroReclamacionesController.php:19
 * @route '/{dominio}/libro-de-reclamaciones'
 */
        store1d3ba87ff951e65d2c2a1c72453353e6Form.post = (args: { dominio: string | number } | [dominio: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store1d3ba87ff951e65d2c2a1c72453353e6.url(args, options),
            method: 'post',
        })
    
    store1d3ba87ff951e65d2c2a1c72453353e6.form = store1d3ba87ff951e65d2c2a1c72453353e6Form
    /**
* @see \App\Http\Controllers\LibroReclamacionesController::store
 * @see app/Http/Controllers/LibroReclamacionesController.php:19
 * @route '/{param1}/{param2}/libro-de-reclamaciones'
 */
const store48e58e99750f44bb2d69c8ad95b2bf98 = (args: { param1: string | number, param2: string | number } | [param1: string | number, param2: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store48e58e99750f44bb2d69c8ad95b2bf98.url(args, options),
    method: 'post',
})

store48e58e99750f44bb2d69c8ad95b2bf98.definition = {
    methods: ["post"],
    url: '/{param1}/{param2}/libro-de-reclamaciones',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\LibroReclamacionesController::store
 * @see app/Http/Controllers/LibroReclamacionesController.php:19
 * @route '/{param1}/{param2}/libro-de-reclamaciones'
 */
store48e58e99750f44bb2d69c8ad95b2bf98.url = (args: { param1: string | number, param2: string | number } | [param1: string | number, param2: string | number ], options?: RouteQueryOptions) => {
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

    return store48e58e99750f44bb2d69c8ad95b2bf98.definition.url
            .replace('{param1}', parsedArgs.param1.toString())
            .replace('{param2}', parsedArgs.param2.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LibroReclamacionesController::store
 * @see app/Http/Controllers/LibroReclamacionesController.php:19
 * @route '/{param1}/{param2}/libro-de-reclamaciones'
 */
store48e58e99750f44bb2d69c8ad95b2bf98.post = (args: { param1: string | number, param2: string | number } | [param1: string | number, param2: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store48e58e99750f44bb2d69c8ad95b2bf98.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\LibroReclamacionesController::store
 * @see app/Http/Controllers/LibroReclamacionesController.php:19
 * @route '/{param1}/{param2}/libro-de-reclamaciones'
 */
    const store48e58e99750f44bb2d69c8ad95b2bf98Form = (args: { param1: string | number, param2: string | number } | [param1: string | number, param2: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store48e58e99750f44bb2d69c8ad95b2bf98.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\LibroReclamacionesController::store
 * @see app/Http/Controllers/LibroReclamacionesController.php:19
 * @route '/{param1}/{param2}/libro-de-reclamaciones'
 */
        store48e58e99750f44bb2d69c8ad95b2bf98Form.post = (args: { param1: string | number, param2: string | number } | [param1: string | number, param2: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store48e58e99750f44bb2d69c8ad95b2bf98.url(args, options),
            method: 'post',
        })
    
    store48e58e99750f44bb2d69c8ad95b2bf98.form = store48e58e99750f44bb2d69c8ad95b2bf98Form

/**
* Multiple routes resolve to \App\Http\Controllers\LibroReclamacionesController::store, so this export is a
* dictionary keyed by URI rather than a callable, with the verbs prefixed where two routes share a URI.
* Call a specific route with `store['<key>'](...)`, or import the route by name from your
* generated `routes/` directory.
*/
export const store = {
    '/libro-de-reclamaciones': store6e1c782efba9f15f83c3af01e6a8009a,
    '/{dominio}/libro-de-reclamaciones': store1d3ba87ff951e65d2c2a1c72453353e6,
    '/{param1}/{param2}/libro-de-reclamaciones': store48e58e99750f44bb2d69c8ad95b2bf98,
}

const LibroReclamacionesController = { store }

export default LibroReclamacionesController