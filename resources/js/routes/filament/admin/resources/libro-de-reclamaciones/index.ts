import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Filament\Resources\LibroReclamaciones\Pages\ListLibroReclamaciones::__invoke
 * @see app/Filament/Resources/LibroReclamaciones/Pages/ListLibroReclamaciones.php:7
 * @route '/admin/libro-de-reclamaciones'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/libro-de-reclamaciones',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\LibroReclamaciones\Pages\ListLibroReclamaciones::__invoke
 * @see app/Filament/Resources/LibroReclamaciones/Pages/ListLibroReclamaciones.php:7
 * @route '/admin/libro-de-reclamaciones'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Resources\LibroReclamaciones\Pages\ListLibroReclamaciones::__invoke
 * @see app/Filament/Resources/LibroReclamaciones/Pages/ListLibroReclamaciones.php:7
 * @route '/admin/libro-de-reclamaciones'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Filament\Resources\LibroReclamaciones\Pages\ListLibroReclamaciones::__invoke
 * @see app/Filament/Resources/LibroReclamaciones/Pages/ListLibroReclamaciones.php:7
 * @route '/admin/libro-de-reclamaciones'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Filament\Resources\LibroReclamaciones\Pages\ListLibroReclamaciones::__invoke
 * @see app/Filament/Resources/LibroReclamaciones/Pages/ListLibroReclamaciones.php:7
 * @route '/admin/libro-de-reclamaciones'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Filament\Resources\LibroReclamaciones\Pages\ListLibroReclamaciones::__invoke
 * @see app/Filament/Resources/LibroReclamaciones/Pages/ListLibroReclamaciones.php:7
 * @route '/admin/libro-de-reclamaciones'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Filament\Resources\LibroReclamaciones\Pages\ListLibroReclamaciones::__invoke
 * @see app/Filament/Resources/LibroReclamaciones/Pages/ListLibroReclamaciones.php:7
 * @route '/admin/libro-de-reclamaciones'
 */
        indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    index.form = indexForm
/**
* @see \App\Filament\Resources\LibroReclamaciones\Pages\EditLibroReclamacion::__invoke
 * @see app/Filament/Resources/LibroReclamaciones/Pages/EditLibroReclamacion.php:7
 * @route '/admin/libro-de-reclamaciones/{record}/edit'
 */
export const edit = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/libro-de-reclamaciones/{record}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\LibroReclamaciones\Pages\EditLibroReclamacion::__invoke
 * @see app/Filament/Resources/LibroReclamaciones/Pages/EditLibroReclamacion.php:7
 * @route '/admin/libro-de-reclamaciones/{record}/edit'
 */
edit.url = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { record: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    record: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        record: args.record,
                }

    return edit.definition.url
            .replace('{record}', parsedArgs.record.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Filament\Resources\LibroReclamaciones\Pages\EditLibroReclamacion::__invoke
 * @see app/Filament/Resources/LibroReclamaciones/Pages/EditLibroReclamacion.php:7
 * @route '/admin/libro-de-reclamaciones/{record}/edit'
 */
edit.get = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})
/**
* @see \App\Filament\Resources\LibroReclamaciones\Pages\EditLibroReclamacion::__invoke
 * @see app/Filament/Resources/LibroReclamaciones/Pages/EditLibroReclamacion.php:7
 * @route '/admin/libro-de-reclamaciones/{record}/edit'
 */
edit.head = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

    /**
* @see \App\Filament\Resources\LibroReclamaciones\Pages\EditLibroReclamacion::__invoke
 * @see app/Filament/Resources/LibroReclamaciones/Pages/EditLibroReclamacion.php:7
 * @route '/admin/libro-de-reclamaciones/{record}/edit'
 */
    const editForm = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: edit.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Filament\Resources\LibroReclamaciones\Pages\EditLibroReclamacion::__invoke
 * @see app/Filament/Resources/LibroReclamaciones/Pages/EditLibroReclamacion.php:7
 * @route '/admin/libro-de-reclamaciones/{record}/edit'
 */
        editForm.get = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: edit.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Filament\Resources\LibroReclamaciones\Pages\EditLibroReclamacion::__invoke
 * @see app/Filament/Resources/LibroReclamaciones/Pages/EditLibroReclamacion.php:7
 * @route '/admin/libro-de-reclamaciones/{record}/edit'
 */
        editForm.head = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: edit.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    edit.form = editForm
const libroDeReclamaciones = {
    index: Object.assign(index, index),
edit: Object.assign(edit, edit),
}

export default libroDeReclamaciones