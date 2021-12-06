<?php

namespace Botble\RealEstate\Commands;

use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Repositories\Interfaces\PropertyInterface;
use Illuminate\Console\Command;
use RealEstateHelper;

class RenewPropertiesCommand extends Command
{
    /**
     * @var PropertyInterface
     */
    public $propertyRepository;

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'cms:properties:renew';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Renew properties';

    /**
     * RenewPropertiesCommand constructor.
     * @param PropertyInterface $propertyRepository
     */
    public function __construct(PropertyInterface $propertyRepository)
    {
        parent::__construct();
        $this->propertyRepository = $propertyRepository;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $properties = $this->propertyRepository->getModel()
            ->expired()
            ->where('moderation_status', ModerationStatusEnum::APPROVED)
            ->where('author_type', Account::class)
            ->join('re_accounts', 're_accounts.id', '=', 're_properties.author_id')
            ->where('re_accounts.credits', '>', 0)
            ->where('re_properties.auto_renew', 1)
            ->with(['author'])
            ->select('re_properties.*')
            ->get();

        foreach ($properties as $property) {
            if ($property->author->credits <= 0) {
                continue;
            }

            $property->expire_date = now()->addDays(RealEstateHelper::propertyExpiredDays());
            $property->save();

            $property->author->credits--;
            $property->author->save();
        }

        $this->info('Renew ' . $properties->count() . ' properties successfully!');
    }
}
