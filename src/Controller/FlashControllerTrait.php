<?php
/*
 * Son T. Tran
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Son T. Tran license that is
 * available through the world-wide-web at this URL:
 * https://www.trants.io/LICENSE.txt
 *
 * DISCLAIMER
 *
 * @author          Son T. Tran
 * @package         trants/paglo
 * @copyright       Copyright (c) Son T. Tran. ( https://trants.io )
 * @license         https://www.trants.io/LICENSE.txt
 *
 */

namespace Trants\Paglo\Controller;

use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use Trants\Paglo\Exceptions\FlashErrorException;
use Trants\Paglo\Helpers\QueryHelper;
use Trants\Paglo\Helpers\RequestHelper;

trait FlashControllerTrait
{
    /**
     * Validates the input data.
     *
     * @param \Illuminate\Http\Request $request
     * @param array $validation
     * @return array
     *
     * @throws \Trants\Paglo\Exceptions\FlashErrorException
     */
    protected function validateAndGetInputs(Request $request, $validation)
    {
        if (isset($validation['fields'])) {
            $inputs = $request->only($validation['fields']);
        } else {
            $inputs = $request->only(array_keys($validation['rules']));
        }

        $validator = Validator::make($inputs, $validation['rules'], isset($validation['messages']) ? $validation['messages'] : []);

        if (!empty($validation['after'])) {
            $validator->after($validation['after']($inputs));
        }

        if ($validator->fails()) {
            throw new FlashErrorException('FormValidationError', null, $validator);
        }

        return $inputs;
    }

    /**
     * Filter external output data easier and quicker.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @param array $filters
     * @return void
     */
    protected function quickFilter($query, Request $request, $filters)
    {
        $inputs = $request->only(array_keys($filters));

        foreach ($filters as $input => $options) {
            $subFilters = isset($options[0]) ? $options : [$options];

            foreach ($subFilters as $options) {
                $checkEmptyInput = array_get($options, 'boolean') === true && isset($inputs[$input]);

                if (empty($inputs[$input]) && !$checkEmptyInput) {
                    continue;
                }

                $field     = empty($options['field']) ? $input : $options['field'];
                $condition = empty($options['condition']) ? '=' : $options['condition'];

                if ($condition == '=' || $condition == '<' || $condition == '<=' || $condition == '>' || $condition == '>=') {
                    $query->where($field, $condition, $inputs[$input]);
                    continue;
                }

                if ($condition == 'in') {
                    $query->whereIn($field, (array)$inputs[$input]);
                    continue;
                }

                if ($condition == 'in_csv_format') {
                    $query->whereIn($field, explode(',', $inputs[$input]));
                    continue;
                }

                if ($condition == 'contains') {
                    $query->where($field, 'like', '%' . QueryHelper::escapeLikeWildcard($inputs[$input]) . '%');
                    continue;
                }

                if ($condition == 'start_with') {
                    $query->where($field, 'like', '%' . QueryHelper::escapeLikeWildcard($inputs[$input]));
                    continue;
                }

                if ($condition == 'end_with') {
                    $query->where($field, 'like', QueryHelper::escapeLikeWildcard($inputs[$input]) . '%');
                    continue;
                }

                if (in_array($condition, ['null_or_eq', 'null_or_gt', 'null_or_gte', 'null_or_lt', 'null_or_lte'])) {
                    $filterValue = $inputs[$input];

                    $query->where(function ($q) use ($field, $filterValue, $condition) {
                        $conditions = ['eq' => '=', 'gt' => '>', 'gte' => '>=', 'lt' => '<', 'lte' => '<='];
                        $condition  = explode('_', $condition)[2];

                        $q->whereNull($field)
                            ->orWhere($field, $conditions[$condition], $filterValue);
                    });
                    continue;
                }
            }
        }
    }

    /**
     * Sort in alphabetical or numerical order, descending or ascending easier and quicker.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @param array $sortFields
     * @param string $input
     * @return void
     */
    protected function quickSort($query, Request $request, $sortFields, $input = 'sort')
    {
        $sort = $request->input($input);

        if ($sort) {
            $sorts = RequestHelper::parseSort($sort, $sortFields);
            foreach ($sorts as $field => $direction) {
                $query->orderBy($field, $direction);
            }
        }
    }

    /**
     * Showing your query result in multiple page easier and quicker.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @param string $input
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    protected function quickPaginate($query, Request $request, $input = 'limit')
    {
        $limit = $request->input($input, 10);

        if ($limit == -1) {
            return $query->get();
        } else {
            $paginator = $query->paginate($limit);
            $paginator->appends($request->query());

            return $paginator;
        }
    }

    /**
     * Filter and arrange in alphabetical or numerical order, descending or ascending easier and quicker.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @param array $filters
     * @param array $sortFields
     * @return void
     */
    protected function filterAndSort($query, Request $request, $filters, $sortFields)
    {
        $this->quickFilter($query, $request, $filters);
        $this->quickSort($query, $request, $sortFields);
    }

    /**
     * Filter and arrange in alphabetical or numerical order, descending or ascending and showing your query result in multiple page easier and quicker.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Trants\EasyJsonApi\ExtendedTransformer $transformer
     * @param \Illuminate\Http\Request $request
     * @param array $filters
     * @param array $sortFields
     * @param array $includes
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function quickIndex($query, $transformer, Request $request, $filters = [], $sortFields = [], $includes = [])
    {
        $this->filterAndSort($query, $request, $filters, $sortFields);

        return flash_data($this->quickPaginate($query, $request), $transformer, $includes);
    }

    /**
     * Filter and arrange in alphabetical or numerical order, descending or ascending and get the first value easier and quicker.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Trants\EasyJsonApi\ExtendedTransformer $transformer
     * @param \Illuminate\Http\Request $request
     * @param array $filters
     * @param array $sortFields
     * @param array $includes
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function quickFirst($query, $transformer, Request $request, $filters = [], $sortFields = [], $includes = [])
    {
        $this->filterAndSort($query, $request, $filters, $sortFields);

        return flash_data($query->first(), $transformer, $includes);
    }

    /**
     * Validate data input and store data with corresponding model easier and quicker.
     *
     * @param \Illuminate\Http\Request $request
     * @param array $validation
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Trants\EasyJsonApi\ExtendedTransformer $transformer
     * @param array $includes
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function quickCreate(Request $request, $validation, $query, $transformer, $includes = [])
    {
        $inputs = $this->validateAndGetInputs($request, $validation);

        $data = $query->create($inputs);

        return flash_data($data, $transformer, $includes);
    }

    /**
     * Display data with corresponding id and model easier and quicker.
     *
     * @param string $id
     * @param string $model
     * @param \Trants\EasyJsonApi\ExtendedTransformer $transformer
     * @param array $includes
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function quickShow($id, $model, $transformer, $includes = [])
    {
        $data = $model::findOrError($id);

        return flash_data($data, $transformer, $includes);
    }

    /**
     * Update and return data with corresponding id and model easier and quicker.
     *
     * @param \Illuminate\Http\Request $request
     * @param array $validation
     * @param string $id
     * @param string $model
     * @param \Trants\EasyJsonApi\ExtendedTransformer $transformer
     * @param array $includes
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function quickUpdate(Request $request, $validation, $id, $model, $transformer, $includes = [])
    {
        $inputs = $this->validateAndGetInputs($request, $validation);

        $data = $model::findOrError($id);
        $data->update($inputs);

        return flash_data($data, $transformer, $includes);
    }

    /**
     * Delete data with corresponding id and model easier and quicker.
     *
     * @param string $id
     * @param string $model
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Trants\Paglo\Exceptions\FlashErrorException
     */
    protected function quickDelete($id, $model)
    {
        $deleted = $model::findOrError($id)->delete();

        if ($deleted) {
            return flash_success();
        } else {
            throw new FlashErrorException(null, 'Cannot delete item');
        }
    }

    /**
     * Delete data with corresponding conditions easier and quicker.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @param array $filters
     * @return \Illuminate\Http\JsonResponse
     */
    protected function quickDeleteAll($query, Request $request, $filters = [])
    {
        $this->quickFilter($query, $request, $filters);
        $query->delete();

        return flash_success();
    }
}
