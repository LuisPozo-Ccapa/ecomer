import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\Secciones\Pages\ListSecciones::__invoke
 * @see app/Filament/Resources/Secciones/Pages/ListSecciones.php:7
 * @route '/admin/secciones/seccions'
 */
const ListSecciones = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListSecciones.url(options),
    method: 'get',
})

ListSecciones.definition = {
    methods: ["get","head"],
    url: '/admin/secciones/seccions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\Secciones\Pages\ListSecciones::__invoke
 * @see app/Filament/Resources/Secciones/Pages/ListSecciones.php:7
 * @route '/admin/secciones/seccions'
 */
ListSecciones.url = (options?: RouteQueryOptions) => {
    return ListSecciones.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Resources\Secciones\Pages\ListSecciones::__invoke
 * @see app/Filament/Resources/Secciones/Pages/ListSecciones.php:7
 * @route '/admin/secciones/seccions'
 */
ListSecciones.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListSecciones.url(options),
    method: 'get',
})
/**
* @see \App\Filament\Resources\Secciones\Pages\ListSecciones::__invoke
 * @see app/Filament/Resources/Secciones/Pages/ListSecciones.php:7
 * @route '/admin/secciones/seccions'
 */
ListSecciones.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ListSecciones.url(options),
    method: 'head',
})

    /**
* @see \App\Filament\Resources\Secciones\Pages\ListSecciones::__invoke
 * @see app/Filament/Resources/Secciones/Pages/ListSecciones.php:7
 * @route '/admin/secciones/seccions'
 */
    const ListSeccionesForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: ListSecciones.url(options),
        method: 'get',
    })

            /**
* @see \App\Filament\Resources\Secciones\Pages\ListSecciones::__invoke
 * @see app/Filament/Resources/Secciones/Pages/ListSecciones.php:7
 * @route '/admin/secciones/seccions'
 */
        ListSeccionesForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: ListSecciones.url(options),
            method: 'get',
        })
            /**
* @see \App\Filament\Resources\Secciones\Pages\ListSecciones::__invoke
 * @see app/Filament/Resources/Secciones/Pages/ListSecciones.php:7
 * @route '/admin/secciones/seccions'
 */
        ListSeccionesForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: ListSecciones.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    ListSecciones.form = ListSeccionesForm
export default ListSecciones