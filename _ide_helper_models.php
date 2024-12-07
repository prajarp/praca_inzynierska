<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $client_name
 * @property string $delivery_address
 * @property string $expected_delivery_date
 * @property int $window_quantity
 * @property int $other_elements_quantity
 * @property float $windows_weight
 * @property float $total_weight
 * @property float $window_area
 * @property string $window_dimensions
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderItem> $orderItems
 * @property-read int|null $order_items_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereClientName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereDeliveryAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereExpectedDeliveryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereOtherElementsQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereTotalWeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereWindowArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereWindowDimensions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereWindowQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereWindowsWeight($value)
 * @mixin \Eloquent
 */
	class Order extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $order_id
 * @property string $item_type
 * @property float $weight
 * @property float $height
 * @property float $width
 * @property float $length
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Order $order
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereItemType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereLength($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereWeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereWidth($value)
 * @mixin \Eloquent
 */
	class OrderItem extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $rack_type
 * @property float $outer_height
 * @property float $outer_width
 * @property float $outer_length
 * @property float $loading_height
 * @property float $loading_width
 * @property float $loading_length
 * @property float $net_weight
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rack newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rack newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rack query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rack whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rack whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rack whereLoadingHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rack whereLoadingLength($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rack whereLoadingWidth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rack whereNetWeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rack whereOuterHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rack whereOuterLength($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rack whereOuterWidth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rack whereRackType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rack whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Rack extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $type
 * @property float $height
 * @property float $width
 * @property float $length
 * @property float $max_weight
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trailer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trailer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trailer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trailer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trailer whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trailer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trailer whereLength($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trailer whereMaxWeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trailer whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trailer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trailer whereWidth($value)
 * @mixin \Eloquent
 */
	class Trailer extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class User extends \Eloquent {}
}

