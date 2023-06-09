# Exchange API Demo App

Demo currency exchange app


## Authentication
Once token is issues for a user you can authencitace with the API by passing `Authroziation` header:
```
Authorization: Bearer __token__
```
## Currency API
### Get all currencies
Endpoint URL (GET): `/api/currency`

```
curl "API_URL/api/currency" \
     -H 'Authorization: Bearer TOKEN' \
     -H 'Accept: application/json'
```

## User API
### Create User
Endpoint URL (POST): `/api/user`

Parameters:
| Parameter     | Required                       | Type     |
|---------------|-------------------------------|-----------|
| email         | Yes                           | string    |
| name          | Yes                           | string    |

```
curl -X "POST" "https://currency-exchange.ddev.site/api/user" \
     -H 'Content-Type: application/json; charset=utf-8' \
     -d $'{
  "email": "email@example.com",
  "name": "Joe Jobson"
}'
```

### Get User
Endpoint URL (GET): `/api/user`

```
curl "https://currency-exchange.ddev.site/api/user" \
     -H 'Authorization: Bearer 3|tneGFNLuMYGgHbTKEIXy2g7WukVTL5pcFPHQBFHs' \
     -H 'Accept: application/json'
```

## Account API
### Create Account
Endpoint URL (POST): `/api/account`

Parameters:
| Parameter     | Required                       | Type     |
|---------------|-------------------------------|-----------|
| currency_id   | Yes                           | int    |

```
curl -X "POST" "https://currency-exchange.ddev.site/api/account" \
     -H 'Authorization: Bearer 3|tneGFNLuMYGgHbTKEIXy2g7WukVTL5pcFPHQBFHs' \
     -H 'Accept: application/json' \
     -H 'Content-Type: application/x-www-form-urlencoded; charset=utf-8' \
     --data-urlencode "currency_id=2"
```

### Get Single Account
Endpoint URL (GET): `/api/account/{uuid}`

**Parameters:**
| Parameter     | Required                       | Type     |
|---------------|------------------------ |-----------|
| uuid   | Yes                           | string    |

```
curl "https://currency-exchange.ddev.site/api/account/ce8ecc17-c2a9-4027-91a0-a870d530b26ea" \
     -H 'Accept: application/json' \
     -H 'Authorization: Bearer 3|tneGFNLuMYGgHbTKEIXy2g7WukVTL5pcFPHQBFHs'
```

### Get All Accounts
Endpoint URL (GET): `/api/accounts`

```
curl "https://currency-exchange.ddev.site/api/accounts" \
     -H 'Accept: application/json' \
     -H 'Authorization: Bearer 3|tneGFNLuMYGgHbTKEIXy2g7WukVTL5pcFPHQBFHs'
```

## Transaction API
### Create Transaction
Endpoint URL (POST): `/api/transaction`

**Parameters:**
| Parameter     | Required                       | Type     |
|---------------|-------------------------------|-----------|
| source_account_id   | Yes                           | int    |
| destination_account_id   | Yes                           | int    |
| amount   | Yes                           | float    |

```
curl -X "POST" "https://currency-exchange.ddev.site/api/transaction" \
     -H 'Accept: application/json' \
     -H 'Authorization: Bearer 3|tneGFNLuMYGgHbTKEIXy2g7WukVTL5pcFPHQBFHs' \
     -H 'Content-Type: application/x-www-form-urlencoded; charset=utf-8' \
     --data-urlencode "source_account_id=1" \
     --data-urlencode "destination_account_id=2" \
     --data-urlencode "amount=500"
```

### Get all transactions
Endpoint URL (GET): `/api/transaction/{?uuid}`

**Parameters:**
| Parameter     | Required                       | Type     |
|---------------|-------------------------------|-----------|
| uuid   | No                           | string    |
You can pass account UUID if you want to filter transactions by account.

**Query parameters:**
| Parameter     | Required                       | Type     |
|---------------|-------------------------------|-----------|
| offset   | No                           | int    |
| limit   | No                           | int    |

```
curl "https://currency-exchange.ddev.site/api/transactions" \
     -H 'Accept: application/json' \
     -H 'Authorization: Bearer 3|tneGFNLuMYGgHbTKEIXy2g7WukVTL5pcFPHQBFHs' \
     -H 'Content-Type: application/x-www-form-urlencoded; charset=utf-8'

curl "https://currency-exchange.ddev.site/api/transactions/ce8ecc17-c2a9-4027-91a0-a870d530b26ea?limit=5" \
     -H 'Accept: application/json' \
     -H 'Authorization: Bearer 3|tneGFNLuMYGgHbTKEIXy2g7WukVTL5pcFPHQBFHs' \
     -H 'Content-Type: application/x-www-form-urlencoded; charset=utf-8'
```

### Get single transaction
Endpoint URL (GET): `/api/transaction/{reference}`

**Parameters:**
| Parameter     | Required                       | Type     |
|---------------|-------------------------------|-----------|
| reference   | yes                           | string    |
You can pass account UUID if you want to filter transactions by account.


```
curl "https://currency-exchange.ddev.site/api/transaction/V0V95VICFMM02FW5E6QE" \
     -H 'Accept: application/json' \
     -H 'Authorization: Bearer 3|tneGFNLuMYGgHbTKEIXy2g7WukVTL5pcFPHQBFHs' \
     -H 'Content-Type: application/x-www-form-urlencoded; charset=utf-8'
```
