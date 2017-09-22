<?php

namespace App\Exceptions\Activation;

use App\Exceptions\InvalidArgument;
use Illuminate\Database\Eloquent\Model;

/**
 * Thrown when tries to activate an already active model
 * @see App\Eloquent\Concerns\Activatable
 */
class ModelAlreadyActive extends InvalidArgument
{

    /** @var Illuminate\Database\Eloquent\Model */
    protected $model;

    /**
     * Create a new AlreadyActive exception.
     *
     * @return void
     */
    public function __construct(Model $model)
    {
        $this->model = $model;

        parent::__construct(__('Model :id is a;ready activated', ['id' => $model->id]));
    }
}
