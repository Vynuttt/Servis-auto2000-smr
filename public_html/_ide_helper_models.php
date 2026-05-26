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
 * @property int $id
 * @property string $booking_code
 * @property int $customer_id
 * @property int $vehicle_id
 * @property int|null $mechanic_id
 * @property string $booking_date
 * @property string $booking_time
 * @property string $service_type
 * @property string|null $notes
 * @property string|null $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $customer
 * @property-read \App\Models\User|null $mechanic
 * @property-read \App\Models\Vehicle $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereBookingCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereBookingDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereBookingTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereMechanicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereServiceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereVehicleId($value)
 */
	class Booking extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $invoice_id
 * @property string $method
 * @property numeric $amount
 * @property \Illuminate\Support\Carbon $paid_at
 * @property string|null $ref_no
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereRefNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereUpdatedAt($value)
 */
	class Payment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereUpdatedAt($value)
 */
	class Role extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WorkOrder> $workOrders
 * @property-read int|null $work_orders_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stall newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stall newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stall query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stall whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stall whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stall whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stall whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stall whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stall whereUpdatedAt($value)
 */
	class Stall extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property int|null $role_id
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $phone
 * @property string|null $avatar
 * @property bool $is_active
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $assignedBookings
 * @property-read int|null $assigned_bookings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $bookings
 * @property-read int|null $bookings_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Vehicle> $vehicles
 * @property-read int|null $vehicles_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role(string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $customer_id
 * @property string $plate_number
 * @property string $brand
 * @property string $model
 * @property string|null $variant
 * @property string|null $year
 * @property string|null $color
 * @property string|null $chassis_no
 * @property string|null $engine_no
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereBrand($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereChassisNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereEngineNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle wherePlateNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereVariant($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereYear($value)
 */
	class Vehicle extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $wo_number
 * @property int|null $booking_id
 * @property int|null $customer_id
 * @property int $vehicle_id
 * @property int|null $mechanic_id
 * @property int|null $stall_id
 * @property string $priority
 * @property string $status
 * @property int|null $current_stage_id
 * @property \Illuminate\Support\Carbon|null $planned_start
 * @property \Illuminate\Support\Carbon|null $planned_finish
 * @property \Illuminate\Support\Carbon|null $actual_start
 * @property \Illuminate\Support\Carbon|null $actual_finish
 * @property int|null $sla_minutes
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Booking|null $booking
 * @property-read \App\Models\WorkOrderStage|null $currentStage
 * @property-read \App\Models\User|null $customer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WorkOrderLog> $logs
 * @property-read int|null $logs_count
 * @property-read \App\Models\User|null $mechanic
 * @property-read \App\Models\Stall|null $stall
 * @property-read \App\Models\Vehicle $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereActualFinish($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereActualStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereBookingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereCurrentStageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereMechanicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder wherePlannedFinish($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder wherePlannedStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereSlaMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereStallId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrder whereWoNumber($value)
 */
	class WorkOrder extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $work_order_id
 * @property string $stage
 * @property \Illuminate\Support\Carbon $started_at
 * @property \Illuminate\Support\Carbon|null $finished_at
 * @property int|null $by_user_id
 * @property string|null $remarks
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @property-read \App\Models\WorkOrder $workOrder
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderLog whereByUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderLog whereFinishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderLog whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderLog whereStage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderLog whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderLog whereWorkOrderId($value)
 */
	class WorkOrderLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int $position
 * @property int $is_final
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderStage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderStage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderStage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderStage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderStage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderStage whereIsFinal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderStage whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderStage wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderStage whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkOrderStage whereUpdatedAt($value)
 */
	class WorkOrderStage extends \Eloquent {}
}

