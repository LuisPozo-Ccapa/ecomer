import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\Secciones\Pages\ListSecciones::__invoke
 * @see app/Filament/Resources/Secciones/Pages/ListSecciones.php:7
 * @route '/admin/secciones/seccions'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/secciones/seccions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\Secciones\Pages\ListSecciones::__invoke
 * @see app/Filament/Resources/Secciones/Pages/ListSecciones.php:7
 * @route '/admin/secciones/seccions'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Resources\Secciones\Pages\ListSecciones::__invoke
 * @see app/Filament/Resources/Secciones/Pages/ListSecciones.php:7
 * @route '/admin/secciones/seccions'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Filament\Resources\Secciones\Pages\ListSecciones::__invoke
 * @see app/Filament/Resources/Secciones/Pages/ListSecciones.php:7
 * @route '/admin/secciones/seccions'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Filament\Resources\Secciones\Pages\ListSecciones::__invoke
 * @see app/Filament/Resources/Secciones/Pages/ListSecciones.php:7
 * @route '/admin/secciones/seccions'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Filament\Resources\Secciones\Pages\ListSecciones::__invoke
 * @see app/Filament/Resources/Secciones/Pages/ListSecciones.php:7
 * @route '/admin/secciones/seccions'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Filament\Resources\Secciones\Pages\ListSecciones::__invoke
 * @see app/Filament/Resources/Secciones/Pages/ListSecciones.php:7
 * @route '/admin/secciones/seccions'
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
const seccions = {
    index: Object.assign(index, index),
}

export default seccions