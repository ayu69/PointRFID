from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
import smtplib
import mimetypes
import email.mime.application

smtp_ssl_host = 'smtp.gmail.com'  # smtp.mail.yahoo.com
smtp_ssl_port = 465
s = smtplib.SMTP_SSL(smtp_ssl_host, smtp_ssl_port)
s.login('formateur.pole@gmail.com', 'Dirtygeek42')

msg = MIMEMultipart()
msg['Subject'] = 'I have a picture'
msg['From'] = 'formateur.pole@gmail.com'
msg['To'] = 'q.marques58@gmail.com'

txt = MIMEText('I just bought a new camera.')
msg.attach(txt)

filename = '/home/pi/RFID/Presence.xlsx' #path to file
fo=open(filename,'rb')
attach = email.mime.application.MIMEApplication(fo.read(),_subtype="pdf")
fo.close()
attach.add_header('Content-Disposition','attachment',filename=filename)
msg.attach(attach)
s.send_message(msg)
s.quit()