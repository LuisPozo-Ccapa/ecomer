import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\Contactos\Pages\EditContacto::__invoke
 * @see app/Filament/Resources/Contactos/Pages/EditContacto.php:7
 * @route '/admin/cotizaciones-y-contacto/{record}/edit'
 */
const EditContacto = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EditContacto.url(args, options),
    method: 'get',
})

EditContacto.definition = {
    methods: ["get","head"],
    url: '/admin/cotizaciones-y-contacto/{record}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\Contactos\Pages\EditContacto::__invoke
 * @see app/Filament/Resources/Contactos/Pages/EditContacto.php:7
 * @route '/admin/cotizaciones-y-contacto/{record}/edit'
 */
EditContacto.url = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return EditContacto.definition.url
            .replace('{record}', parsedArgs.record.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Filament\Resources\Contactos\Pages\EditContacto::__invoke
 * @see app/Filament/Resources/Contactos/Pages/EditContacto.php:7
 * @route '/admin/cotizaciones-y-contacto/{record}/edit'
 */
EditContacto.get = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EditContacto.url(args, options),
    method: 'get',
})
/**
* @see \App\Filament\Resources\Contactos\Pages\EditContacto::__invoke
 * @see app/Filament/Resources/Contactos/Pages/EditContacto.php:7
 * @route '/admin/cotizaciones-y-contacto/{record}/edit'
 */
EditContacto.head = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: EditContacto.url(args, options),
    method: 'head',
})

    /**
* @see \App\Filament\Resources\Contactos\Pages\EditContacto::__invoke
 * @see app/Filament/Resources/Contactos/Pages/EditContacto.php:7
 * @route '/admin/cotizaciones-y-contacto/{record}/edit'
 */
    const EditContactoForm = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: EditContacto.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Filament\Resources\Contactos\Pages\EditContacto::__invoke
 * @see app/Filament/Resources/Contactos/Pages/EditContacto.php:7
 * @route '/admin/cotizaciones-y-contacto/{record}/edit'
 */
        EditContactoForm.get = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: EditContacto.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Filament\Resources\Contactos\Pages\EditContacto::__invoke
 * @see app/Filament/Resources/Contactos/Pages/EditContacto.php:7
 * @route '/admin/cotizaciones-y-contacto/{record}/edit'
 */
        EditContactoForm.head = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: EditContacto.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    EditContacto.form = EditContactoForm
export default EditContacto