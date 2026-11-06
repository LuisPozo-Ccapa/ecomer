import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\LibroReclamaciones\Pages\EditLibroReclamacion::__invoke
 * @see app/Filament/Resources/LibroReclamaciones/Pages/EditLibroReclamacion.php:7
 * @route '/admin/libro-de-reclamaciones/{record}/edit'
 */
const EditLibroReclamacion = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EditLibroReclamacion.url(args, options),
    method: 'get',
})

EditLibroReclamacion.definition = {
    methods: ["get","head"],
    url: '/admin/libro-de-reclamaciones/{record}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\LibroReclamaciones\Pages\EditLibroReclamacion::__invoke
 * @see app/Filament/Resources/LibroReclamaciones/Pages/EditLibroReclamacion.php:7
 * @route '/admin/libro-de-reclamaciones/{record}/edit'
 */
EditLibroReclamacion.url = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return EditLibroReclamacion.definition.url
            .replace('{record}', parsedArgs.record.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Filament\Resources\LibroReclamaciones\Pages\EditLibroReclamacion::__invoke
 * @see app/Filament/Resources/LibroReclamaciones/Pages/EditLibroReclamacion.php:7
 * @route '/admin/libro-de-reclamaciones/{record}/edit'
 */
EditLibroReclamacion.get = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EditLibroReclamacion.url(args, options),
    method: 'get',
})
/**
* @see \App\Filament\Resources\LibroReclamaciones\Pages\EditLibroReclamacion::__invoke
 * @see app/Filament/Resources/LibroReclamaciones/Pages/EditLibroReclamacion.php:7
 * @route '/admin/libro-de-reclamaciones/{record}/edit'
 */
EditLibroReclamacion.head = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: EditLibroReclamacion.url(args, options),
    method: 'head',
})

    /**
* @see \App\Filament\Resources\LibroReclamaciones\Pages\EditLibroReclamacion::__invoke
 * @see app/Filament/Resources/LibroReclamaciones/Pages/EditLibroReclamacion.php:7
 * @route '/admin/libro-de-reclamaciones/{record}/edit'
 */
    const EditLibroReclamacionForm = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: EditLibroReclamacion.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Filament\Resources\LibroReclamaciones\Pages\EditLibroReclamacion::__invoke
 * @see app/Filament/Resources/LibroReclamaciones/Pages/EditLibroReclamacion.php:7
 * @route '/admin/libro-de-reclamaciones/{record}/edit'
 */
        EditLibroReclamacionForm.get = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: EditLibroReclamacion.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Filament\Resources\LibroReclamaciones\Pages\EditLibroReclamacion::__invoke
 * @see app/Filament/Resources/LibroReclamaciones/Pages/EditLibroReclamacion.php:7
 * @route '/admin/libro-de-reclamaciones/{record}/edit'
 */
        EditLibroReclamacionForm.head = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: EditLibroReclamacion.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    EditLibroReclamacion.form = EditLibroReclamacionForm
export default EditLibroReclamacion