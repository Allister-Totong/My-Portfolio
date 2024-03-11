def encode(msg, shift):
    newmsg = ""
    msg = list(msg)
    for x in range(len(msg)):
    #A for loop to check each ch in msg:
        ch = msg[x]
        if ('A' <= ch and ch <= 'Z'): #if the character is an uppercase letter
            newchr = chr((ord(ch) + shift - 65) % 26 + 65)
            newmsg = newmsg + newchr
        elif ('a' <= ch and ch <= 'z'): #if the character is a lowercase letter
            newchr = chr((ord(ch) + shift - 97) % 26 + 97)
            newmsg = newmsg + newchr
        else: #if the character is not alphabetic
            #Hint: What does the assignment ask you to do with such character?
            newmsg = newmsg + ch
        #After the character is transformed, not put it as part of the new message
        #Hint: which variables hold the new message and new character?
        

    return newmsg

msg = input("Enter message to be encrypted: ")
shiftstr = input("Enter shift amount (1-25): ")
shift = int(shiftstr) 

print("Encrypted message: " + encode(msg, shift))
