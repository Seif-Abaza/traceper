<?php

require_once("../bootstrap.php");

class UsersTest extends CDbTestCase
{
	
 	public $fixtures=array( 			
 			'users'=>'Users',
 			);
	
	
	public function testTableName()
	{
	    $this->assertEquals(Users::model()->tableName(),'traceper_users');  	    
	}
	
	public function testSaveUser() {
		$email = "test@test.com";
		$password = "1231231";
		$realname = "test";
		$userType = UserType::RealUser;
		$accountType = 0;
		$this->assertTrue(Users::model()->saveUser($email, $password, $realname, $userType, $accountType));
		
		$rows = Users::model()->findAll("email=:email", array(":email"=>$email));
		
		$this->assertEquals(count($rows), 1);
		$this->assertEquals($rows[0]->email, $email);
		$this->assertEquals($rows[0]->password, $password);
		$this->assertEquals($rows[0]->realname, $realname);
		$this->assertEquals($rows[0]->userType, $userType);
		$this->assertEquals($rows[0]->account_type, $accountType);
		
		
		try {
			//try to register same e-mail address, it should throw exception
			Users::model()->saveUser($email, "1232424", "deneme traceper", UserType::GPSStaff, 0);
			$this->assertTrue(false);
		}
		catch (CDbException $exp){
			$this->assertTrue(true);
		}
		//try to register with missing parameters 
		$this->assertFalse(Users::model()->saveUser("dene@deneme.com", "", "", "", ""));
		$this->assertFalse(Users::model()->saveUser("", "1232424", "", "", ""));
		$this->assertFalse(Users::model()->saveUser("", "", "", "", ""));
		$this->assertFalse(Users::model()->saveUser("dene@deneme.com", "1232424", "", "", ""));
		$this->assertFalse(Users::model()->saveUser("", "1232424", "deneme traceper", "", ""));
		$this->assertFalse(Users::model()->saveUser("dene@deneme.com", "", "deneme traceper", "", ""));
		$this->assertFalse(Users::model()->saveUser("", "", "deneme traceper", "", ""));
		$this->assertFalse(Users::model()->saveUser("", "", "", UserType::GPSDevice, ""));
		$this->assertFalse(Users::model()->saveUser("", "", "", "", 1));
		
		//try to register with a wrong formatted email address
		$this->assertFalse(Users::model()->saveUser("denedeneme.com", "1232424", "deneme traceper", UserType::RealStaff, 0));
	}
	
	
	public function testUpdateLocation(){
		
		$this->assertTrue($this->users("user1")->save());
		
		$rows = Users::model()->findAll("email=:email", array(":email"=>$this->users("user1")->email));
		
		$this->assertEquals(count($rows), 1);
		
		$latitude = 12.123455;
		$longitude = 123.345566;
		$altitude = 12313;
		$deviceId = 3342232;
		$calculatedTime = "2012-12-02 12:01:01";
		$userId = $rows[0]['Id'];
		
		$effectedRows = Users::model()->updateLocation($latitude, $longitude, $altitude, $deviceId, $calculatedTime, $userId);
		
		$this->assertEquals($effectedRows, 1);
		
		$result = Users::model()->findByPk($rows[0]['Id']);
		
		//testing if it is saved accurately
		$this->assertEquals($latitude, $result->latitude);
		$this->assertEquals($longitude, $result->longitude);
		$this->assertEquals($altitude, $result->altitude);
		$this->assertEquals($deviceId, $result->deviceId);
		$this->assertEquals($calculatedTime, $result->dataCalculatedTime);
		
		
		$latitude = -89.123433;
		$longitude = -179.123233;
		
		$effectedRows = Users::model()->updateLocation($latitude, $longitude, $altitude, $deviceId, $calculatedTime, $userId);
		
		$result = Users::model()->findByPk($rows[0]['Id']);
		
		//testing if it is saved accurately
		$this->assertEquals($latitude, $result->latitude);
		$this->assertEquals($longitude, $result->longitude);
		$this->assertEquals($altitude, $result->altitude);
		$this->assertEquals($deviceId, $result->deviceId);
		$this->assertEquals($calculatedTime, $result->dataCalculatedTime);
	}
	
	public function testChangePassword()
	{
		//register user for testing...
		$this->assertTrue($this->users("user1")->save());
		$password = rand(1232323, 989899999);
		$this->assertTrue(Users::model()->changePassword($this->users("user1")->Id, $password));

		// try to login with new password to check if it is changed correctly...
		$identity=new UserIdentity($this->users("user1")->email, $password);
		$this->assertEquals($identity->authenticate(), CUserIdentity::ERROR_NONE);
	}
	
	public function testGetUserIdReturnsInteger()
	{
		$this->assertTrue($this->users("user1")->save());
		
		//Check whether the method returns an integer value when the queried email exits in DB
		$this->assertInternalType("integer", Users::model()->getUserId($this->users("user1")->email));
	}
	
	public function testGetUserIdReturnsNullForInvalidEmail()
	{
		$this->assertTrue($this->users("user1")->save());
		$this->assertTrue($this->users("user2")->save());

		//Check whether the method returns null value, when the queried email does not exist in DB
		$this->assertNull(Users::model()->getUserId("invalidEmail"));
	}

	public function testGetUserIdReturnsTrueIdForGivenEmail()
	{
		$this->assertTrue($this->users("user1")->save());
		$this->assertTrue($this->users("user2")->save());

		//Check whether the method returns the true Id for the given e-mail
		$this->assertEquals($this->users("user1")->Id, Users::model()->getUserId($this->users("user1")->email));
		$this->assertEquals($this->users("user2")->Id, Users::model()->getUserId($this->users("user2")->email));
	}	

	public function testDeleteUserReturnsNullForNonExistingId()
	{
		$this->assertTrue($this->users("user1")->save()); //Id:1
		$this->assertTrue($this->users("user2")->save()); //Id:2
	
		//Check whether the method returns null value, when the queried Id does not exist in DB
		$this->assertNull(Users::model()->deleteUser("0"));
		$this->assertNull(Users::model()->deleteUser("3"));
	}

	public function testDeleteUser()
	{
		$this->assertTrue($this->users("user1")->save()); //Id:1
		$this->assertTrue($this->users("user2")->save()); //Id:2
	
		//Check whether the method returns true, when the queried Id exists in DB
		$this->assertTrue(Users::model()->deleteUser($this->users("user1")->Id));
		$this->assertTrue(Users::model()->deleteUser($this->users("user2")->Id));
	}	
}