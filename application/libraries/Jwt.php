<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jwt
{
	public function encode($payload, $secretKey, $alg='HS512')
	{
		$header = array('typ' => 'JWT', 'alg' => $alg);
		$jwtHeader = $this->urlsafeB64Encode($this->jsonEncode($header));
		$jwtPayload = $this->urlsafeB64Encode($this->jsonEncode($payload));
		$signingInput = $jwtHeader.'.'.$jwtPayload;
		$jwtSignature = $this->sign($signingInput, $secretKey, $alg);
		$jwtSignature = $this->urlsafeB64Encode($jwtSignature);
		return $jwtHeader.'.'.$jwtPayload.'.'.$jwtSignature;
	}

	public function decode($jwt, $secretKey=NULL, $verify=TRUE)
	{
		$decodedInfo['status'] = TRUE;
		$decodedInfo['payload'] = NULL;

		$jwt = str_replace('Bearer ', '', $jwt);
		$segments = explode('.', $jwt);

		if (count($segments) != 3):
			$decodedInfo['status'] = FALSE;
			$decodedInfo['feedback'] = 'Número incorreto de segmentos';
			return $decodedInfo;
		endif;

		$headerB64 = $segments[0];
		$payloadB64 = $segments[1];
		$signatureB64 = $segments[2];

		if (($header = $this->jsonDecode($this->urlsafeB64Decode($headerB64))) === NULL):
			$decodedInfo['status'] = FALSE;
			$decodedInfo['feedback'] = 'Codificação de segmento inválido!';
			return $decodedInfo;
		endif;

		if (($payload = $this->jsonDecode($this->urlsafeB64Decode($payloadB64))) === NULL):
			$decodedInfo['status'] = FALSE;
			$decodedInfo['feedback'] = 'Codificação de segmento inválido!';
			return $decodedInfo;
		endif;

		if ($verify):
			$signature = $this->urlsafeB64Decode($signatureB64);
			if (empty($header->alg)):
				$decodedInfo['status'] = FALSE;
				$decodedInfo['feedback'] = 'Algoritmo não definido!';
				return $decodedInfo;
			endif;

			if ($signature != $this->sign($headerB64.'.'.$payloadB64, $secretKey, $header->alg)):
				$decodedInfo['status'] = FALSE;
				$decodedInfo['feedback'] = 'Erro na verificação de assinatura!';
				return $decodedInfo;
			endif;
		endif;

		$decodedInfo['payload'] = $payload;
		return $decodedInfo;
	}

	public function sign($message, $secretKey, $method='HS512')
	{
		$methods = array(
			'HS512' => 'sha512',
		);

		if (empty($methods[$method])):
			throw new DomainException('Algorithm not supported');
		endif;

		return hash_hmac($methods[$method], $message, $secretKey, TRUE);
	}

	public function jsonEncode($input)
	{
		$json = json_encode($input);
		if (function_exists('json_last_error') && $errno = json_last_error()):
			//////////////
			// LOG ERRO //
			//////////////
			return NULL;
		elseif ($json === 'null' && $input !== NULL):
			//////////////
			// LOG ERRO //
			//////////////
			return NULL;
		endif;
		return $json;
	}

	public function jsonDecode($input)
	{
		$obj = json_decode($input);
		if (function_exists('json_last_error') && $errno = json_last_error()):
			//////////////
			// LOG ERRO //
			//////////////
			return NULL;
		elseif ($obj === NULL && $input !== 'null'):
			//////////////
			// LOG ERRO //
			//////////////
			return NULL;
		endif;
		return $obj;
	}

	// Codifica a string com URL-safe Base64
	public function urlsafeB64Encode($input)
	{
		$input = strtr(base64_encode($input), '+/', '-_');
		return str_replace('=', '', $input);
	}

	// Decodifica a string com URL-safe Base64
	public function urlsafeB64Decode($input)
	{
		$remainder = strlen($input) % 4;
		if ($remainder) {
			$padlen = 4 - $remainder;
			$input .= str_repeat('=', $padlen);
		}
		return base64_decode(strtr($input, '-_', '+/'));
	}

}

/* End of file JWT.php */
/* Location: ./application/libraries/api/JWT.php */