<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ShortUrlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
    }

    private function createCompany(string $name = 'Test Company'): Company
    {
        return Company::create(['name' => $name]);
    }

    private function createUser(string $role, ?int $companyId = null): User
    {
        return User::create([
            'name'       => ucfirst($role) . ' User',
            'email'      => $role . '@example.com',
            'password'   => bcrypt('password'),
            'role'       => $role,
            'company_id' => $companyId,
        ]);
    }

    #[Test]
    public function admin_can_create_short_url()
    {
        $company = $this->createCompany();
        $admin = $this->createUser('admin', $company->id);

        $response = $this->actingAs($admin)->post('/short-urls', [
            'original_url' => 'https://google.com',
        ]);

        $response->assertRedirect(route('short-urls.index'));
        $this->assertDatabaseHas('short_urls', ['original_url' => 'https://google.com']);
    }

    #[Test]
    public function member_can_create_short_url()
    {
        $company = $this->createCompany();
        $member = $this->createUser('member', $company->id);

        $response = $this->actingAs($member)->post('/short-urls', [
            'original_url' => 'https://github.com',
        ]);

        $response->assertRedirect(route('short-urls.index'));
        $this->assertDatabaseHas('short_urls', ['original_url' => 'https://github.com']);
    }

    #[Test]
    public function superadmin_cannot_create_short_url()
    {
        $superadmin = $this->createUser('superadmin');

        $response = $this->actingAs($superadmin)->post('/short-urls', [
            'original_url' => 'https://amazon.com',
        ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_can_only_see_urls_of_their_own_company()
    {
        $company1 = $this->createCompany('Company One');
        $company2 = $this->createCompany('Company Two');

        $admin = $this->createUser('admin', $company1->id);

        $member1 = User::create(['name' => 'M1', 'email' => 'm1@example.com', 'password' => bcrypt('password'), 'role' => 'member', 'company_id' => $company1->id]);
        $member2 = User::create(['name' => 'M2', 'email' => 'm2@example.com', 'password' => bcrypt('password'), 'role' => 'member', 'company_id' => $company2->id]);

        ShortUrl::create(['user_id' => $member1->id, 'company_id' => $company1->id, 'original_url' => 'https://company1.com', 'short_code' => 'abc123']);
        ShortUrl::create(['user_id' => $member2->id, 'company_id' => $company2->id, 'original_url' => 'https://company2.com', 'short_code' => 'xyz789']);

        $response = $this->actingAs($admin)->get('/short-urls');

        $response->assertSee('https://company1.com');
        $response->assertDontSee('https://company2.com');
    }

    #[Test]
    public function member_can_only_see_their_own_urls()
    {
        $company = $this->createCompany();

        $member1 = User::create(['name' => 'M1', 'email' => 'm1@example.com', 'password' => bcrypt('password'), 'role' => 'member', 'company_id' => $company->id]);
        $member2 = User::create(['name' => 'M2', 'email' => 'm2@example.com', 'password' => bcrypt('password'), 'role' => 'member', 'company_id' => $company->id]);

        ShortUrl::create(['user_id' => $member1->id, 'company_id' => $company->id, 'original_url' => 'https://member1.com', 'short_code' => 'mem111']);
        ShortUrl::create(['user_id' => $member2->id, 'company_id' => $company->id, 'original_url' => 'https://member2.com', 'short_code' => 'mem222']);

        $response = $this->actingAs($member1)->get('/short-urls');

        $response->assertSee('https://member1.com');
        $response->assertDontSee('https://member2.com');
    }

    #[Test]
    public function short_url_is_publicly_resolvable()
    {
        $company = $this->createCompany();
        $member = $this->createUser('member', $company->id);

        ShortUrl::create([
            'user_id'      => $member->id,
            'company_id'   => $company->id,
            'original_url' => 'https://laravel.com',
            'short_code'   => 'test99',
        ]);

        $response = $this->get('/s/test99');

        $response->assertRedirect('https://laravel.com');
    }
}
