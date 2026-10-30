import { Buffer } from 'buffer';

/** @author Thiago Delgado Pinto */
export function encodeToBase64(text: string): string {
	return Buffer.from(text).toString('base64');
}

export function decodeFromBase64(base64String: string): string {
	return Buffer.from(base64String, 'base64').toString('utf8');
}
