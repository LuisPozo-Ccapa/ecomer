import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\LibroReclamaciones\Pages\ListLibroReclamaciones::__invoke
 * @see app/Filament/Resources/LibroReclamaciones/Pages/ListLibroReclamaciones.php:7
 * @route '/admin/libro-de-reclamaciones'
 */
const ListLibroReclamaciones = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListLibroReclamaciones.url(options),
    method: 'get',
})

ListLibroReclamaciones.definition = {
    methods: ["get","head"],
    url: '/admin/libro-de-reclamaciones',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\LibroReclamaciones\Pages\ListLibroReclamaciones::__invoke
 * @see app/Filament/Resources/LibroReclamaciones/Pages/ListLibroReclamaciones.php:7
 * @route '/admin/libro-de-reclamaciones'
 */
ListLibroReclamaciones.url = (options?: RouteQueryOptions) => {
    return ListLibroReclamaciones.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Resources\LibroReclamaciones\Pages\ListLibroReclamaciones::__invoke
 * @see app/Filament/Resources/LibroReclamaciones/Pages/ListLibroReclamaciones.php:7
 * @route '/admin/libro-de-reclamaciones'
 */
ListLibroReclamaciones.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListLibroReclamaciones.url(options),
    method: 'get',
})
/**
* @see \App\Filament\Resources\LibroReclamaciones\Pages\ListLibroReclamaciones::__invoke
 * @see app/Filament/Resources/LibroReclamaciones/Pages/ListLibroReclamaciones.php:7
 * @route '/admin/libro-de-reclamaciones'
 */
ListLibroReclamaciones.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ListLibroReclamaciones.url(options),
    method: 'head',
})

    /**
* @see \App\Filament\Resources\LibroReclamaciones\Pages\ListLibroReclamaciones::__invoke
 * @see app/Filament/Resources/LibroReclamaciones/Pages/ListLibroReclamaciones.php:7
 * @route '/admin/libro-de-reclamaciones'
 */
    const ListLibroReclamacionesForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: ListLibroReclamaciones.url(options),
        method: 'get',
    })

            /**
* @see \App\Filament\Resources\LibroReclamaciones\Pages\ListLibroReclamaciones::__invoke
 * @see app/Filament/Resources/LibroReclamaciones/Pages/ListLibroReclamaciones.php:7
 * @route '/admin/libro-de-reclamaciones'
 */
        ListLibroReclamacionesForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: ListLibroReclamaciones.url(options),
            method: 'get',
        })
            /**
* @see \App\Filament\Resources\LibroReclamaciones\Pages\ListLibroReclamaciones::__invoke
 * @see app/Filament/Resources/LibroReclamaciones/Pages/ListLibroReclamaciones.php:7
 * @route '/admin/libro-de-reclamaciones'
 */
        ListLibroReclamacionesForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: ListLibroReclamaciones.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    ListLibroReclamaciones.form = ListLibroReclamacionesForm
export default ListLibroReclamaciones