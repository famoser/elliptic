# Python pseudo-code from RFC7784, patched to make it work. Notably, the following changes:
# - Replace [ord(b) for b in k] by list(bytes.fromhex(u))
# - Cast argument to range (in decodeLittleEndian) to int()

def decodeLittleEndian(b, bits):
   return sum([b[i] << 8 * i for i in range(int((bits + 7) / 8))])

def decodeUCoordinate(u, bits):
   u_list = list(bytes.fromhex(u))
   # Ignore any unused bits.
   if bits % 8:
       u_list[-1] &= (1<<(bits%8))-1
   return decodeLittleEndian(u_list, bits)

def decodeScalar25519(k):
   k_list = list(bytes.fromhex(k))
   k_list[0] &= 248
   k_list[31] &= 127
   k_list[31] |= 64
   return decodeLittleEndian(k_list, 255)

def decodeScalar448(k):
   k_list = list(bytes.fromhex(k))
   k_list[0] &= 252
   k_list[55] |= 128
   return decodeLittleEndian(k_list, 448)

print(decodeScalar25519('a546e36bf0527c9d3b16154b82465edd62144c0ac1fc5a18506a2244ba449ac4'))
print(decodeUCoordinate('e6db6867583030db3594c1a424b15f7c726624ec26b3353b10a903a6d0ab1c4c', 255))

print(decodeScalar25519('4b66e9d4d1b4673c5ad22691957d6af5c11b6421e0ea01d42ca4169e7918ba0d'))
print(decodeUCoordinate('e5210f12786811d3f4b7959d0538ae2c31dbe7106fc03c3efc4cd549c715a493', 255))


print(decodeScalar448('3d262fddf9ec8e88495266fea19a34d28882acef045104d0d1aae121700a779c984c24f8cdd78fbff44943eba368f54b29259a4f1c600ad3'))
print(decodeUCoordinate('06fce640fa3487bfda5f6cf2d5263f8aad88334cbd07437f020f08f9814dc031ddbdc38c19c6da2583fa5429db94ada18aa7a7fb4ef8a086', 448))

print(decodeScalar448('203d494428b8399352665ddca42f9de8fef600908e0d461cb021f8c538345dd77c3e4806e25f46d3315c44e0a5b4371282dd2c8d5be3095f'))
print(decodeUCoordinate('0fbcc2f993cd56d3305b0b7d9e55d4c1a8fb5dbb52f8e9a1e9b6201b165d015894e56c4d3570bee52fe205e28a78b91cdfbde71ce8d157db', 448))
