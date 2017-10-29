from PIL import Image

# with open("msf.png") as f:
    # import pdb;pdb.set_trace()
image = Image.open("msf.png")
# image.convert("RGB")
image.load(scale=2)
