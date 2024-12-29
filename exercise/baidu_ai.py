import hashlib

# 原始明文
plaintext = "abingou2016@#"

# 计算 MD5 哈希值
md5_hash = hashlib.md5(plaintext.encode()).hexdigest()

print(f"明文: {plaintext}")
print(f"MD5 密文: {md5_hash}")