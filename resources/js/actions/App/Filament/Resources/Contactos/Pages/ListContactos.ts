import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\Contactos\Pages\ListContactos::__invoke
 * @see app/Filament/Resources/Contactos/Pages/ListContactos.php:7
 * @route '/admin/cotizaciones-y-contacto'
 */
const ListContactos = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListContactos.url(options),
    method: 'get',
})

ListContactos.definition = {
    methods: ["get","head"],
    url: '/admin/cotizaciones-y-contacto',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\Contactos\Pages\ListContactos::__invoke
 * @see app/Filament/Resources/Contactos/Pages/ListContactos.php:7
 * @route '/admin/cotizaciones-y-contacto'
 */
ListContactos.url = (options?: RouteQueryOptions) => {
    return ListContactos.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Resources\Contactos\Pages\ListContactos::__invoke
 * @see app/Filament/Resources/Contactos/Pages/ListContactos.php:7
 * @route '/admin/cotizaciones-y-contacto'
 */
ListContactos.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListContactos.url(options),
    method: 'get',
})
/**
* @see \App\Filament\Resources\Contactos\Pages\ListContactos::__invoke
 * @see app/Filament/Resources/Contactos/Pages/ListContactos.php:7
 * @route '/admin/cotizaciones-y-contacto'
 */
ListContactos.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ListContactos.url(options),
    method: 'head',
})

    /**
* @see \App\Filament\Resources\Contactos\Pages\ListContactos::__invoke
 * @see app/Filament/Resources/Contactos/Pages/ListContactos.php:7
 * @route '/admin/cotizaciones-y-contacto'
 */
    const ListContactosForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: ListContactos.url(options),
        method: 'get',
    })

            /**
* @see \App\Filament\Resources\Contactos\Pages\ListContactos::__invoke
 * @see app/Filament/Resources/Contactos/Pages/ListContactos.php:7
 * @route '/admin/cotizaciones-y-contacto'
 */
        ListContactosForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: ListContactos.url(options),
            method: 'get',
        })
            /**
* @see \App\Filament\Resources\Contactos\Pages\ListContactos::__invoke
 * @see app/Filament/Resources/Contactos/Pages/ListContactos.php:7
 * @route '/admin/cotizaciones-y-contacto'
 */
        ListContactosForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: ListContactos.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    ListContactos.form = ListContactosForm
export default ListContactos