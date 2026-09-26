import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\ProductController::view
 * @see app/Http/Controllers/ProductController.php:42
 * @route '/'
 */
export const view = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: view.url(options),
    method: 'get',
})

view.definition = {
    methods: ["get","head"],
    url: '/',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ProductController::view
 * @see app/Http/Controllers/ProductController.php:42
 * @route '/'
 */
view.url = (options?: RouteQueryOptions) => {
    return view.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ProductController::view
 * @see app/Http/Controllers/ProductController.php:42
 * @route '/'
 */
view.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: view.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\ProductController::view
 * @see app/Http/Controllers/ProductController.php:42
 * @route '/'
 */
view.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: view.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\ProductController::view
 * @see app/Http/Controllers/ProductController.php:42
 * @route '/'
 */
    const viewForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: view.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\ProductController::view
 * @see app/Http/Controllers/ProductController.php:42
 * @route '/'
 */
        viewForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: view.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\ProductController::view
 * @see app/Http/Controllers/ProductController.php:42
 * @route '/'
 */
        viewForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: view.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    view.form = viewForm
/**
* @see \App\Http\Controllers\ProductController::index
 * @see app/Http/Controllers/ProductController.php:16
 * @route '/checkout/{id}'
 */
export const index = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/checkout/{id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ProductController::index
 * @see app/Http/Controllers/ProductController.php:16
 * @route '/checkout/{id}'
 */
index.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    id: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        id: args.id,
                }

    return index.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ProductController::index
 * @see app/Http/Controllers/ProductController.php:16
 * @route '/checkout/{id}'
 */
index.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\ProductController::index
 * @see app/Http/Controllers/ProductController.php:16
 * @route '/checkout/{id}'
 */
index.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\ProductController::index
 * @see app/Http/Controllers/ProductController.php:16
 * @route '/checkout/{id}'
 */
    const indexForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\ProductController::index
 * @see app/Http/Controllers/ProductController.php:16
 * @route '/checkout/{id}'
 */
        indexForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\ProductController::index
 * @see app/Http/Controllers/ProductController.php:16
 * @route '/checkout/{id}'
 */
        indexForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    index.form = indexForm
const ProductController = { view, index }

export default ProductController