<?php namespace Olssonm\IdentityNumber\Tests;

use Validator;
use Olssonm\IdentityNumber\Pin as Pin;
use Olssonm\IdentityNumber\IdentityNumberFormatter as IdentityNumberFormatter;

class IdentityNumberTest extends \Orchestra\Testbench\TestCase {

	public function setUp() {
        parent::setUp();
    }

    /**
     * Load the package
     * @return array the packages
     */
    protected function getPackageProviders($app)
    {
        return [
            'Olssonm\IdentityNumber\IdentityNumberServiceProvider'
        ];
    }

	/** @test */
	public function test_identity_number_formatter()
	{
		$test1 = new IdentityNumberFormatter('19860210-7313', 12, true);
		$this->assertEquals('19860210-7313', $test1->getFormatted());

		$test2 = new IdentityNumberFormatter('19860210-7313', 10, true);
		$this->assertEquals('860210-7313', $test2->getFormatted());

		$test3 = new IdentityNumberFormatter('19860210-7313', 10, false);
		$this->assertEquals('8602107313', $test3->getFormatted());

		$test4 = new IdentityNumberFormatter('19121012+4412', 12, true);
		$this->assertEquals('19121012+4412', $test4->getFormatted());

		$test5 = new IdentityNumberFormatter('19121012+4412', 10, true);
		$this->assertEquals('121012+4412', $test5->getFormatted());

		$test6 = new IdentityNumberFormatter('19121012+4412', 10, false);
		$this->assertEquals('1210124412', $test6->getFormatted());

		$test7 = new IdentityNumberFormatter('19860210 + 1100', 10, false);
		$this->assertEquals('8602101100', $test7->clean()->getFormatted());

		$test8 = new IdentityNumberFormatter('aa19860210-1100bb', 10, true);
		$this->assertEquals('860210-1100', $test8->clean()->getFormatted());
	}

	/** @test */
	public function test_standalone_correct_identity_numbers()
	{
		$this->assertTrue(Pin::isValid('600411-8177'));
        $this->assertTrue(Pin::isValid('19860210-7313'));
		$this->assertTrue(Pin::isValid('600411+8177'));
        $this->assertTrue(Pin::isValid('19860210+7313'));
        $this->assertTrue(Pin::isValid('8905247188', 'identity'));
        $this->assertTrue(Pin::isValid('196711202850', 'identity'));
	}

	/** @test */
	public function test_standalone_incorrect_identity_numbers() {
		$this->assertFalse(Pin::isValid('600412-8177'));
        $this->assertFalse(Pin::isValid('19860211-7313'));
        $this->assertFalse(Pin::isValid('8905257188'));
        $this->assertFalse(Pin::isValid('196711212850'));

		// Malformed
		$this->assertFalse(Pin::isValid('890'));
		$this->assertFalse(Pin::isValid('aaa999'));

		// Obviously false
		$this->assertFalse(Pin::isValid('00000000-0000'));
		$this->assertFalse(Pin::isValid('11111111-1111'));
		$this->assertFalse(Pin::isValid('22222222-2222'));
		$this->assertFalse(Pin::isValid('33333333-3333'));
		$this->assertFalse(Pin::isValid('44444444-4444'));
		$this->assertFalse(Pin::isValid('55555555-5555'));
		$this->assertFalse(Pin::isValid('66666666-6666'));
		$this->assertFalse(Pin::isValid('77777777-7777'));
		$this->assertFalse(Pin::isValid('88888888-8888'));
		$this->assertFalse(Pin::isValid('99999999-9999'));
	}

	/** @test */
	public function test_standalone_correct_organization_numbers()
	{
		$this->assertTrue(Pin::isValid('556016-0680', 'organization')); // Ericsson AB
		$this->assertTrue(Pin::isValid('556103-4249', 'organization')); // Telia AB
	}

	/** @test */
	public function test_standalone_incorrect_organization_numbers()
	{
		$this->assertFalse(Pin::isValid('556016-0681', 'organization')); // Ericsson AB
		$this->assertFalse(Pin::isValid('556103-4240', 'organization')); // Telia AB

		// Malformed
		$this->assertFalse(Pin::isValid('55601', 'organization'));
		$this->assertFalse(Pin::isValid('5561035', 'organization'));
		$this->assertFalse(Pin::isValid('aaa888', 'organization'));
	}

	/** @test */
	public function test_standalone_correct_coordination_numbers()
	{
		$this->assertTrue(Pin::isValid('780161-1117', 'coordination'));
		$this->assertTrue(Pin::isValid('19610280-2425', 'coordination'));
	}

	/** @test */
	public function test_standalone_incorrect_coordination_numbers()
	{
		$this->assertFalse(Pin::isValid('780161-1116', 'coordination'));
		$this->assertFalse(Pin::isValid('19610280-2424', 'coordination'));

		// Malformed
		$this->assertFalse(Pin::isValid('7801', 'coordination'));
		$this->assertFalse(Pin::isValid('19610280-242', 'coordination'));
		$this->assertFalse(Pin::isValid('aaa888', 'coordination'));
	}

	/** @test */
    public function test_standalone_gibberish_data()
	{
		$this->assertFalse(Pin::isValid(null));
        $this->assertFalse(Pin::isValid(false));
        $this->assertFalse(Pin::isValid(true));
        $this->assertFalse(Pin::isValid(111000));
        $this->assertFalse(Pin::isValid(191919191919));
        $this->assertFalse(Pin::isValid(19870101));
		$this->assertFalse(Pin::isValid('780161 - 1117', 'coordination'));
        $this->assertFalse(Pin::isValid('Firstname Lastname'));
        $this->assertFalse(Pin::isValid('Gibberish'));
	}

	/** @test */
	public function test_coordination_workflow()
	{
		$this->assertFalse(Pin::isValid('19860210 - 7313', 'coordination'));
		$this->assertTrue(Pin::isValid((new IdentityNumberFormatter('a19860210 - 7313', 10, true))->clean()->getFormatted(), 'coordination'));
	}

	/** @test */
	public function test_correct_identity_numbers()
	{
        $this->assertTrue($this->validate('600411-8177'));
        $this->assertTrue($this->validate('19860210-7313'));
        $this->assertTrue($this->validate('8905247188'));
        $this->assertTrue($this->validate('196711202850'));
	}

	/** @test */
	public function test_correct_identity_numbers_with_plus_as_hyphen()
	{
        $this->assertTrue($this->validate('600411+8177'));
        $this->assertTrue($this->validate('19860210+7313'));
	}

    /** @test */
    public function test_incorrect_identity_numbers()
	{
        $this->assertFalse($this->validate('600412-8177'));
        $this->assertFalse($this->validate('19860211-7313'));
        $this->assertFalse($this->validate('8905257188'));
        $this->assertFalse($this->validate('196711212850'));
		// Obviously false
		$this->assertFalse($this->validate('000000000000'));
		$this->assertFalse($this->validate('111111111111'));
		$this->assertFalse($this->validate('222222222222'));
		$this->assertFalse($this->validate('333333333333'));
		$this->assertFalse($this->validate('444444444444'));
		$this->assertFalse($this->validate('555555555555'));
		$this->assertFalse($this->validate('666666666666'));
		$this->assertFalse($this->validate('777777777777'));
		$this->assertFalse($this->validate('888888888888'));
		$this->assertFalse($this->validate('999999999999'));
	}

	/** @test **/
	public function test_correct_organization_numbers()
	{
		$this->assertTrue($this->validateOrgNo('556809-9963')); // IKEA AB
		$this->assertTrue($this->validateOrgNo('969663-7033')); // Skellefteå Energi Underhåll Handelsbolag
	}

	/** @test **/
	public function test_incorrect_organization_numbers()
	{
		// Standalone
		$this->assertFalse(Pin::isValid('556016-0681', false)); // Ericsson AB
		$this->assertFalse(Pin::isValid('556103-4240', false)); // Telia AB

		// Validation
		$this->assertFalse($this->validateOrgNo('556809-9964')); // IKEA AB
		$this->assertFalse($this->validateOrgNo('969663-7034')); // Skellefteå Energi Underhåll Handelsbolag

		// Validate so that companies org. numbers doesn't pass as a PIN
		$this->assertFalse(Pin::isValid('556016-0681')); // Ericsson AB
		$this->assertFalse(Pin::isValid('556103-4240')); // Telia AB
	}

	/** @test **/
	public function test_correct_coordination_numbers()
	{
		$this->assertTrue($this->validateCoordNo('6102802425'));
		$this->assertTrue($this->validateCoordNo('19890362-4529'));
	}

	/** @test **/
	public function test_incorrect_coordination_numbers()
	{
		$this->assertFalse($this->validateCoordNo('6102802424'));
		$this->assertFalse($this->validateCoordNo('6102202425'));
		$this->assertFalse($this->validateCoordNo('19890362-4528'));
		$this->assertFalse($this->validateCoordNo('19890302-4529'));
	}

	/** @test **/
	public function test_org_no_as_pin()
	{
		// Validate so that companies org. numbers doesn't pass as a PIN
		$this->assertFalse(Pin::isValid('556016-0681', 'organization')); // Ericsson AB
		$this->assertFalse(Pin::isValid('556103-4240', 'organization')); // Telia AB
		$this->assertFalse($this->validate('556809-9964')); // IKEA AB
		$this->assertFalse($this->validate('969663-7034')); // Skellefteå Energi Underhåll Handelsbolag
	}

    /** @test */
    public function test_gibberish_data()
	{
		$this->assertFalse($this->validate(null));
        $this->assertFalse($this->validate(false));
        $this->assertFalse($this->validate(true));
        $this->assertFalse($this->validate(111000));
        $this->assertFalse($this->validate(191919191919));
        $this->assertFalse($this->validate(19870822));
        $this->assertFalse($this->validate('Firstname Lastname'));
        $this->assertFalse($this->validate('Gibberish'));
	}

	/** @test */
	public function test_error_message()
	{
		$this->assertEquals('A standard message', $this->validateWithErrorMessage('600412-8177', 'A standard message'));
		$this->assertEquals('validation.identity_number', $this->validateWithErrorMessage('600412-8177', null));
		$this->assertEquals(true, $this->validateWithErrorMessage('600412-8177', true));
		$this->assertEquals(false, $this->validateWithErrorMessage('600412-8177', false));
	}

    /**
     * Validate personal identity number
     * @param  mixed $pin	the personal identity number
     * @return bool         whether the validation passes or not
     */
    private function validate($pin) {
        $data = ['identity_no' => $pin];
        $validator = Validator::make($data, [
            'identity_no' => 'identity_number|required',
        ]);

        return $validator->passes();
    }

	/**
     * Validate org no
     * @param  mixed $pin	the personal identity number
     * @return bool         whether the validation passes or not
     */
    private function validateOrgNo($number) {
        $data = ['org_no' => $number];
        $validator = Validator::make($data, [
            'org_no' => 'organization_number|required',
        ]);

        return $validator->passes();
    }

	/**
     * Validate coordination number
     * @param  mixed $pin	the personal identity number
     * @return bool         whether the validation passes or not
     */
    private function validateCoordNo($number) {
        $data = ['coordination_no' => $number];
        $validator = Validator::make($data, [
            'coordination_no' => 'coordination_number|required',
        ]);

        return $validator->passes();
    }

	/**
     * Validate with error message
     * @param  mixed $pin	 the personal identity number
     * @return bool          whether the validation passes or not
     */
    private function validateWithErrorMessage($pin, $message) {
        $data = ['pnr' => $pin];
        $validator = Validator::make($data, [
            'pnr' => 'identity_number',
        ],[
			'pnr.identity_number' => $message
		]);

		$errors = $validator->errors();

        return $errors->first('pnr');
    }
}
